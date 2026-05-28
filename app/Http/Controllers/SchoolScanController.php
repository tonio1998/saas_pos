<?php

namespace App\Http\Controllers;

use App\Models\ScanLogs;
use App\Services\Attendance\AttendanceModeService;
use App\Services\Attendance\AttendanceStatusService;
use App\Services\SMS\SMSManager;
use App\Services\SMS\SMSMessageBuilder;
use App\Models\School;
use App\Models\SmsQueuingModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SchoolScanController extends Controller
{
    public function scan(Request $request)
    {
        try {

            $request->validate([
                'code' => 'required|string|max:255',
            ]);

            $schoolId = session('school_id');

            $settings = cache(
                'school_settings_' . $schoolId
            );

            $schoolId = $settings?->id;

            $input = trim($request->code);

            if (str_contains($input, '@')) {

                $input = explode('@', $input)[0];
            }

            $query = User::with([
                'roles',
                'studentInfo.guardian',
                'teacherInfo',
                'guardianInfo'
            ]);

            if (ctype_digit($input)) {

                $normalizedInput = ltrim($input, '0');

                $query->where(function ($q) use (
                    $input,
                    $normalizedInput
                ) {

                    $q->where('qr_code', $input)
                        ->orWhere('id', $input)
                        ->orWhereRaw(
                            'CAST(nfc_code AS UNSIGNED) = ?',
                            [$normalizedInput ?: 0]
                        );
                });

            } else {

                $query->where(function ($q) use ($input) {

                    $q->where('nfc_code', $input)
                        ->orWhere('qr_code', $input);
                });
            }

            $user = $query->first();

            if (!$user) {

                return response()->json([
                    'status' => 'denied',
                    'message' => 'User not found.',
                    'name' => 'Unknown User',
                    'role' => [],
                    'photo' => asset('images/avatar.png'),
                    'time' => now()->format('h:i A')
                ], 404);
            }

            $roles = $user->getRoleNames();

            $modeData = app(
                AttendanceModeService::class
            )->resolve($user->id);

            $mode = $modeData['mode'];

            $direction = $modeData['direction'];

            $attendanceStatus = app(
                AttendanceStatusService::class
            )->resolve(
                $user->id,
                $mode,
                $settings
            );

            $scanLog = ScanLogs::create([
                'UserID' => $user->id,
                'Mode' => $mode,
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'status' => 'active',
                'archived' => 0,
                'scan_type' => 'nfc',
                'direction' => $direction,
                'attendance_status' => $attendanceStatus,
                'school_id' => $schoolId,
            ]);

            $verificationCode = 'VC' . str_pad(
                    $scanLog->id,
                    5,
                    '0',
                    STR_PAD_LEFT
                );

            $scanLog->VerificationCode = $verificationCode;

            $scanLog->save();

            $phoneNumbers = [];

            $dest = 'guardian';

            if ($roles->contains('students')) {

                $phoneNumbers[] =
                    $user->studentInfo?->guardian?->PhoneNumber;
                $dest = 'guardian';
            }

            if ($roles->contains('parents')) {

                $phoneNumbers[] =
                    $user->guardianInfo?->PhoneNumber;
                $dest = 'own';
            }

            if ($roles->contains('employees')) {

                $phoneNumbers[] =
                    $user->teacherInfo?->PhoneNumber;
                $dest = 'own';
            }

//            dd($phoneNumbers);

            $phoneNumbers = array_unique(
                array_filter($phoneNumbers)
            );

            $schoolName = $settings?->SchoolName
                ?? env('SCHOOL_NAME', 'School');

            $message = app(
                SMSMessageBuilder::class
            )->build(
                $user,
                $direction,
                $attendanceStatus,
                $verificationCode,
                $schoolName,
                $dest
            );

            $smsEnabled = (int) (
                $settings?->sms_enabled ?? 1
            );

//            dd($smsEnabled);
            $sendID = 0;
            if ($smsEnabled === 1) {

                foreach ($phoneNumbers as $number) {

                    $cleanNumber = preg_replace(
                        '/[^0-9]/',
                        '',
                        $number
                    );

                    if (strlen($cleanNumber) >= 10) {
                        try {
                            $sendID = $this->queueSMSSend(
                                $cleanNumber,
                                $message
                            );

                        } catch (\Throwable $smsError) {
//                            dd($smsError);
                            report($smsError);
                        }
                    }
                }
            }

//            dd($sendID);

            return response()->json([
                'status' => 'success',

                'message' => $mode === 1
                    ? 'Entry recorded successfully.'
                    : 'Exit recorded successfully.',

                'attendance_status' => $attendanceStatus,

                'mode' => $mode === 1
                    ? 'TIME_IN'
                    : 'TIME_OUT',

                'name' => $user->name,

                'role' => $roles
                    ->map(fn($role) => ucfirst($role))
                    ->values(),

                'photo' => $user->filepath
                    ? asset('storage/' . $user->filepath)
                    : asset('images/avatar.png'),

                'time' => $scanLog->created_at
                    ->format('h:i A'),

                'verification_code' => $verificationCode
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'status' => 'denied',

                'message' => $e->validator
                    ->errors()
                    ->first(),

                'errors' => $e->errors(),

                'name' => 'Validation Error',

                'role' => [],

                'photo' => asset('images/avatar.png'),

                'time' => now()->format('h:i A')

            ], 422);

        } catch (\Throwable $e) {

            report($e);

            return response()->json([
                'status' => 'denied',

                'message' => config('app.debug')
                    ? $e->getMessage()
                    : 'Something went wrong. Please try again.',

                'name' => 'System Error',

                'role' => [],

                'photo' => asset('images/avatar.png'),

                'time' => now()->format('h:i A')

            ], 500);
        }
    }

    private function queueSMSSend($phoneNumber, $message)
    {
        $queue = new SmsQueuingModel();
        $queue->school_id = session('school_id');
        $queue->PhoneNumber = $phoneNumber;
        $queue->Message = $message;
        $queue->remark = "pending";
        $queue->created_at = now();
        $queue->updated_at = now();
        $queue->status = 'active';
        $queue->archived = 0;
        $queue->created_by = 0;
        $queue->updated_by = 0;
        $queue->save();

        return $queue;
    }
}
