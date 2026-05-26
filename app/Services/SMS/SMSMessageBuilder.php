<?php

namespace App\Services\SMS;

class SMSMessageBuilder
{
    public function build(
        $user,
        string $direction,
        string $attendanceStatus,
        string $verificationCode,
        string $schoolName
    ): string {

        $entryText = $direction === 'entry'
            ? 'entered'
            : 'left';

        $attendanceLabel = match ($attendanceStatus) {
            'late' => ' (LATE)',
            'early_out' => ' (EARLY OUT)',
            default => ''
        };

        return (
            $user->studentInfo?->guardian?->LastName
                ? 'Dear Mr/Mrs. '
                . $user->studentInfo->guardian->LastName
                . ",\n\n"
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
    }
}
