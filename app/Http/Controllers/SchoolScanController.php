<?php

namespace App\Http\Controllers;

use App\Models\ScanLogs;
use App\Models\SchoolSetting;
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
            $settings = cache('school_settings_' . $schoolId);
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
                $query->where(function ($q) use ($input, $normalizedInput) {
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
            $lastLog = ScanLogs::whereDate('created_at', today())
                ->where('UserID', $user->id)
                ->latest()
                ->first();

            $mode = 1;

            if ($lastLog) {
                $mode = $lastLog->Mode == 1 ? 0 : 1;
            }

            $direction = $mode === 1
                ? 'entry'
                : 'exit';

            $attendanceStatus = 'present';

            $lateGraceMinutes = (int) (
                $settings?->LateGraceMinutes ?? 15
            );

            $now = now();

            $morningInTime = Carbon::today()
                ->setTimeFromTimeString(
                    $settings?->OfficialTimeIn ?? '07:00:00'
                );

            $lunchOutTime = Carbon::today()
                ->setTime(12, 0);

            $afternoonInTime = Carbon::today()
                ->setTime(13, 0);

            $finalOutTime = Carbon::today()
                ->setTimeFromTimeString(
                    $settings?->OfficialTimeOut ?? '17:00:00'
                );

            $todayLogs = ScanLogs::whereDate(
                'created_at',
                today()
            )
                ->where('UserID', $user->id);

            if ($mode === 1) {

                $isMorningSession = $now->lt($lunchOutTime);

                if ($isMorningSession) {

                    $alreadyMorningIn = (clone $todayLogs)
                        ->where('Mode', 1)
                        ->whereTime('created_at', '<', '12:00:00')
                        ->exists();

                    if (!$alreadyMorningIn) {

                        $lateLimit = $morningInTime
                            ->copy()
                            ->addMinutes($lateGraceMinutes);

                        if ($now->greaterThan($lateLimit)) {
                            $attendanceStatus = 'late';
                        }
                    }

                } else {

                    $alreadyAfternoonIn = (clone $todayLogs)
                        ->where('Mode', 1)
                        ->whereTime('created_at', '>=', '12:00:00')
                        ->exists();

                    if (!$alreadyAfternoonIn) {

                        $lateLimit = $afternoonInTime
                            ->copy()
                            ->addMinutes($lateGraceMinutes);

                        if ($now->greaterThan($lateLimit)) {
                            $attendanceStatus = 'late';
                        }
                    }
                }
            }

            if ($mode === 0) {

                $isLunchOut = $now->lt($afternoonInTime);

                if ($isLunchOut) {

                    $alreadyLunchOut = (clone $todayLogs)
                        ->where('Mode', 0)
                        ->whereTime('created_at', '<', '13:00:00')
                        ->exists();

                    if (!$alreadyLunchOut) {

                        if ($now->lessThan($lunchOutTime)) {
                            $attendanceStatus = 'early_out';
                        }
                    }

                } else {

                    $alreadyFinalOut = (clone $todayLogs)
                        ->where('Mode', 0)
                        ->whereTime('created_at', '>=', '13:00:00')
                        ->exists();

                    if (!$alreadyFinalOut) {

                        if ($now->lessThan($finalOutTime)) {
                            $attendanceStatus = 'early_out';
                        }
                    }
                }
            }

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

            $verificationCode = 'VC-' . str_pad(
                    $scanLog->id,
                    10,
                    '0',
                    STR_PAD_LEFT
                );

            $scanLog->VerificationCode = $verificationCode;
            $scanLog->save();

            $phoneNumbers = [];

            if ($roles->contains('students')) {
                $phoneNumbers[] = $user->studentInfo?->guardian?->PhoneNumber;
            }

            if ($roles->contains('parents')) {
                $phoneNumbers[] = $user->guardianInfo?->PhoneNumber;
            }

            if ($roles->contains('employees')) {
                $phoneNumbers[] = $user->teacherInfo?->PhoneNumber;
            }

            $phoneNumbers = array_unique(
                array_filter($phoneNumbers)
            );

            $schoolName = $settings?->SchoolName
                ?? env('SCHOOL_NAME', 'School');

            $entryText = $direction === 'entry'
                ? 'entered'
                : 'left';

            $attendanceLabel = match ($attendanceStatus) {
                'late' => ' (LATE)',
                'early_out' => ' (EARLY OUT)',
                default => ''
            };

            $message = (
                $user->studentInfo?->guardian?->LastName
                    ? 'Dear Mr/Mrs. '
                    . $user->studentInfo->guardian->LastName
                    . ",\n"
                    : ''
                )
                . $user->name
                . ' just '
                . $entryText
                . ' '
                . $schoolName
                . $attendanceLabel
                . ' @ '
                . now()->format('M d, Y h:i:s A')
                . '. Code: '
                . $verificationCode;

            $smsEnabled = (int) (
                $settings?->sms_enabled ?? 1
            );

            if ($smsEnabled === 1) {

                foreach ($phoneNumbers as $number) {

                    $cleanNumber = preg_replace(
                        '/[^0-9]/',
                        '',
                        $number
                    );

                    if (strlen($cleanNumber) >= 10) {

                        try {

                            $this->queueSMSSend(
                                $cleanNumber,
                                $message
                            );

                        } catch (\Throwable $smsError) {

                            report($smsError);
                        }
                    }
                }
            }

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
    }
}
