<?php

namespace App\Services\Attendance;

use App\Models\ScanLogs;
use Carbon\Carbon;

class AttendanceStatusService
{
    public function resolve(
        int $userId,
        int $mode,
            $settings
    ): string {

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
            ->where('UserID', $userId);

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

        return $attendanceStatus;
    }
}
