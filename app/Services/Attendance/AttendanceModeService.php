<?php

namespace App\Services\Attendance;

use App\Models\ScanLogs;

class AttendanceModeService
{
    public function resolve(int $userId): array
    {
        $lastLog = ScanLogs::whereDate(
            'created_at',
            today()
        )
            ->where('UserID', $userId)
            ->latest()
            ->first();

        $mode = 1;

        if ($lastLog) {
            $mode = $lastLog->Mode == 1 ? 0 : 1;
        }

        return [
            'mode' => $mode,
            'direction' => $mode === 1
                ? 'entry'
                : 'exit',
        ];
    }
}
