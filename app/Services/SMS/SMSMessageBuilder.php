<?php

namespace App\Services\SMS;

class SMSMessageBuilder
{
    public function build(
        $user,
        string $direction,
        string $attendanceStatus,
        string $verificationCode,
        string $schoolName,
        string $dest
    ): string {

        $action = match ($direction) {

            'entry'
            => 'TIME IN',

            'exit'
            => 'TIME OUT',

            default
            => 'ATTENDANCE',
        };

        $attendanceRemark = match ($attendanceStatus) {

            'late'
            => ' [LATE]',

            'early_out'
            => ' [EARLY OUT]',

            default
            => '',
        };

        $timestamp = now()
            ->format(
                'M d, Y h:i A'
            );

        if ($dest === 'own') {

            return

                'Good day, '

                . $user->name

                . '. Your '

                . $action

                . ' attendance at '

                . $schoolName

                . ' has been successfully recorded'

                . $attendanceRemark

                . '. '

                . 'Date & Time: '

                . $timestamp

                . '. Ref#: '

                . $verificationCode

                . '.';
        }

        $guardianLastName =
            $user->studentInfo
                ?->guardian
                ?->LastName;

        $guardianText =
            $guardianLastName
                ? 'Dear Mr./Mrs. '
                . $guardianLastName
                . ', '
                : 'Dear Parent/Guardian, ';

        return

            $guardianText

            . 'please be informed that '

            . $user->name

            . '\'s '

            . $action

            . ' attendance at '

            . $schoolName

            . ' has been successfully recorded'

            . $attendanceRemark

            . '. '

            . 'Date & Time: '

            . $timestamp

            . '. Ref#: '

            . $verificationCode

            . '.';
    }
}
