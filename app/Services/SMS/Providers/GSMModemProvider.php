<?php

namespace App\Services\SMS\Providers;

use App\Services\SMS\Contracts\SMSProviderInterface;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class GSMModemProvider implements SMSProviderInterface
{
    public function send(
        string $phone,
        string $message
    ): array {

        try {

            $settings = system_settings();

            if (!$settings->python_path) {

                return [
                    'success' => false,
                    'remark' => 'failed',
                    'message' => 'Python path not configured.',
                ];
            }

            if (!$settings->port_com) {

                return [
                    'success' => false,
                    'remark' => 'failed',
                    'message' => 'COM port not configured.',
                ];
            }

            $pythonScript = base_path(
                'sms/send_sms.py'
            );

            if (!file_exists($pythonScript)) {

                return [
                    'success' => false,
                    'remark' => 'failed',
                    'message' => 'send_sms.py not found.',
                ];
            }

            $message = iconv(
                'UTF-8',
                'ASCII//TRANSLIT//IGNORE',
                $message
            );

            $message = trim($message);

            $message = substr(
                $message,
                0,
                120
            );

            Log::info('GSM SEND START', [
                'phone' => $phone,
                'message' => $message,
                'port' => $settings->port_com,
            ]);

            $process = new Process([
                $settings->python_path,
                $pythonScript,
                $phone,
                $message,
                $settings->port_com,
            ]);

            $process->setTimeout(60);

            $process->run();

            $success = $process->isSuccessful();

            $errorOutput = trim(
                $process->getErrorOutput()
            );

            $standardOutput = trim(
                $process->getOutput()
            );

            Log::info('GSM PROCESS RESULT', [
                'success' => $success,
                'output' => $standardOutput,
                'error' => $errorOutput,
                'exit_code' => $process->getExitCode(),
            ]);

            if (!$success) {

                return [
                    'success' => false,
                    'remark' => 'failed',
                    'message' => 'SMS sending failed.',
                    'error' => $errorOutput ?: $standardOutput,
                    'exit_code' => $process->getExitCode(),
                ];
            }

            return [
                'success' => true,
                'remark' => 'sent',
                'message' => 'SMS sent successfully.',
                'output' => $standardOutput,
            ];

        } catch (\Throwable $e) {

            Log::error('GSM SEND EXCEPTION', [
                'phone' => $phone,
                'message' => $message,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'remark' => 'failed',
                'message' => 'Unexpected GSM exception.',
                'error' => $e->getMessage(),
            ];
        }
    }
}
