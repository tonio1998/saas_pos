<?php

namespace App\Services\SMS\Providers;

use App\Services\SMS\Contracts\SMSProviderInterface;
use Illuminate\Support\Collection;
use Symfony\Component\Process\Process;

class GSMModemProvider implements SMSProviderInterface
{
    public function send(
        string $phone,
        string $message
    ): bool {

        $messages = collect([
            (object) [

                'id'
                => 0,

                'PhoneNumber'
                => $phone,

                'Message'
                => $message,
            ]
        ]);

        $results = $this->bulkSend(
            $messages
        );

        return $results[0]['success']
            ?? false;
    }

    public function bulkSend(
        Collection $messages
    ): array {

        $settings = system_settings();

        if (
            !$settings->python_path
        ) {

            $settings->update([

                'sms_status'
                => 'error',

                'sms_signal_status'
                => 'offline',

                'sms_last_error'
                => 'Python path not configured.',

                'remark'
                => 'Python path not configured.',
            ]);

            throw new \Exception(
                'Python path not configured.'
            );
        }

        if (
            !$settings->port_com
        ) {

            $settings->update([

                'sms_status'
                => 'error',

                'sms_signal_status'
                => 'offline',

                'sms_last_error'
                => 'COM port not configured.',

                'remark'
                => 'COM port not configured.',
            ]);

            throw new \Exception(
                'COM port not configured.'
            );
        }

        $pythonScript = base_path(
            'sms/send_bulk_sms.py'
        );

        if (
            !file_exists(
                $pythonScript
            )
        ) {

            $settings->update([

                'sms_status'
                => 'error',

                'sms_signal_status'
                => 'offline',

                'sms_last_error'
                => 'send_bulk_sms.py not found.',

                'remark'
                => 'Python bulk script missing.',
            ]);

            throw new \Exception(
                'send_bulk_sms.py not found.'
            );
        }

        $settings->update([

            'sms_status'
            => 'processing',

            'sms_signal_status'
            => 'processing',

            'sms_last_error'
            => null,

            'remark'
            => 'Bulk SMS processing...',
        ]);

        $payload = $messages
            ->map(function ($sms) {

                $message = iconv(
                    'UTF-8',
                    'ASCII//TRANSLIT//IGNORE',
                    $sms->Message
                );

                $message = trim(
                    preg_replace(
                        '/\s+/',
                        ' ',
                        $message
                    )
                );

                return [

                    'id'
                    => $sms->id,

                    'phone'
                    => trim(
                        $sms->PhoneNumber
                    ),

                    'message'
                    => $message,
                ];
            })
            ->values()
            ->toJson();

        $process = new Process([

            $settings->python_path,

            $pythonScript,

            $payload,

            $settings->port_com,
        ]);

        $process->setTimeout(
            300
        );

        $process->setIdleTimeout(
            300
        );

        $process->run();

        if (
            !$process->isSuccessful()
        ) {

            $errorOutput = trim(
                $process
                    ->getErrorOutput()
            );

            $standardOutput = trim(
                $process
                    ->getOutput()
            );

            $friendlyError =
                $errorOutput
                    ?: 'Bulk GSM sending failed.';

            $signalStatus =
                'unknown';

            $lowerError = strtolower(
                $friendlyError
            );

            if (
                str_contains(
                    $lowerError,
                    '+creg: 0,0'
                )
            ) {

                $signalStatus =
                    'offline';

                $friendlyError =
                    'SIM not connected to GSM network';
            }

            elseif (
                str_contains(
                    $lowerError,
                    '+creg: 0,2'
                )
            ) {

                $signalStatus =
                    'searching';

                $friendlyError =
                    'Searching GSM network';
            }

            elseif (
                str_contains(
                    $lowerError,
                    'weak signal'
                )
            ) {

                $signalStatus =
                    'weak';

                $friendlyError =
                    'Weak GSM signal';
            }

            elseif (
                str_contains(
                    $lowerError,
                    'timeout'
                )
            ) {

                $signalStatus =
                    'slow';

                $friendlyError =
                    'SMS timeout';
            }

            $settings->increment(
                'sms_failed_count'
            );

            $settings->update([

                'sms_status'
                => 'error',

                'sms_signal_status'
                => $signalStatus,

                'sms_last_error'
                => $friendlyError,

                'sms_last_failed_at'
                => now(),

                'remark'
                => $friendlyError,
            ]);

            throw new \Exception(

                "BULK GSM SEND FAILED\n\n"

                . "PORT: "

                . $settings->port_com

                . "\n\n"

                . "EXIT CODE: "

                . $process->getExitCode()

                . "\n\n"

                . "ERROR OUTPUT:\n"

                . (
                $errorOutput
                    ?: 'EMPTY'
                )

                . "\n\n"

                . "STANDARD OUTPUT:\n"

                . (
                $standardOutput
                    ?: 'EMPTY'
                )
            );
        }

        $output = trim(
            $process->getOutput()
        );

        $decoded = json_decode(
            $output,
            true
        );

        if (
            !is_array($decoded)
        ) {

            $settings->update([

                'sms_status'
                => 'error',

                'sms_signal_status'
                => 'unknown',

                'sms_last_error'
                => 'Invalid bulk GSM response.',

                'remark'
                => 'Invalid GSM response.',
            ]);

            throw new \Exception(
                'Invalid bulk GSM response.'
            );
        }

        $successCount = collect(
            $decoded
        )
            ->where(
                'success',
                true
            )
            ->count();

        if ($successCount > 0) {

            $settings->increment(
                'total_sent',
                $successCount
            );
        }

        $settings->update([

            'sms_status'
            => 'online',

            'sms_signal_status'
            => 'good',

            'sms_last_error'
            => null,

            'remark'
            => 'SMS operational',
        ]);

        return $decoded;
    }

}
