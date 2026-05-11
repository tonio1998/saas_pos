<?php

namespace App\Http\Controllers;

use App\Models\ScanLogs;
use App\Models\SchoolSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ScanController extends Controller
{
    public function scan(Request $request)
    {
        try {

            $request->validate([
                'code' => 'required|string|max:255'
            ]);

            $input = trim($request->code);

            $query = User::with([
                'roles',
                'studentInfo.guardian',
                'teacherInfo',
                'guardianInfo'
            ]);

            if (ctype_digit($input)) {
                $query->where(function ($q) use ($input) {
                    $q->where('qr_code', $input)
                        ->orWhere('id', $input)
                        ->orWhere('nfc_code', $input);
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

            $lastLog = ScanLogs::where('UserID', $user->id)
                ->latest('id')
                ->first();

            $mode = ($lastLog && (int) $lastLog->Mode === 1) ? 0 : 1;

            $direction = $mode === 1 ? 'entry' : 'exit';

            do {

                $verificationCode = strtoupper(Str::random(16));

            } while (
                ScanLogs::where('VerificationCode', $verificationCode)->exists()
            );

            $settings = Cache::remember(
                'school_settings',
                now()->addHours(12),
                function () {

                    return SchoolSetting::pluck(
                        'setting_value',
                        'setting_key'
                    )->toArray();
                }
            );

            $officialTimeIn = $settings['official_time_in'] ?? '07:30';
            $gracePeriod = (int) ($settings['grace_period_minutes'] ?? 10);

            $attendanceStatus = 'present';

            $todayOfficialTime = Carbon::today()
                ->setTimeFromTimeString($officialTimeIn);

            $allowedTime = $todayOfficialTime
                ->copy()
                ->addMinutes($gracePeriod);

            $scanLog = ScanLogs::create([
                'UserID' => $user->id,
                'Mode' => $mode,
                'VerificationCode' => $verificationCode,
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'status' => 'active',
                'archived' => 0,
                'scan_type' => 'nfc',
                'direction' => $mode === 1 ? 'entry' : 'exit',
                'attendance_status' => $attendanceStatus,
            ]);

            $schoolName = $settings['school_name']
                ?? env('SCHOOL_NAME', 'School');

            $entryText = $direction === 'entry'
                ? 'entered'
                : 'left';

            $lateText = $attendanceStatus === 'late'
                ? ' (LATE)'
                : '';

            $message = $user->name
                . ' just '
                . $entryText
                . ' '
                . $schoolName
                . $lateText
                . ' @ '
                . now()->format('M d, Y h:i:s A')
                . '. Code: '
                . $verificationCode;

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

            $smsEnabled = (int) ($settings['sms_enabled'] ?? 0);

            if ($smsEnabled === 1) {

                foreach ($phoneNumbers as $number) {

                    $cleanNumber = preg_replace('/[^0-9]/', '', $number);

                    if (strlen($cleanNumber) >= 10) {

                        try {

                            queueSMSSend($cleanNumber, $message);

                        } catch (\Throwable $smsError) {

                            report($smsError);
                        }
                    }
                }
            }

            return response()->json([
                'status' => $mode === 1 ? 'in' : 'out',
                'message' => $mode === 1
                    ? 'Entry recorded successfully.'
                    : 'Exit recorded successfully.',
                'attendance_status' => $attendanceStatus,
                'official_time_in' => $officialTimeIn,
                'grace_period_minutes' => $gracePeriod,
                'name' => $user->name,
                'role' => $roles
                    ->map(fn($role) => ucfirst($role))
                    ->values(),
                'photo' => $user->filepath
                    ? asset('storage/' . $user->filepath)
                    : asset('images/avatar.png'),
                'time' => $scanLog->created_at->format('h:i A'),
                'verification_code' => $verificationCode
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'status' => 'denied',
                'message' => $e->validator->errors()->first(),
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
}
