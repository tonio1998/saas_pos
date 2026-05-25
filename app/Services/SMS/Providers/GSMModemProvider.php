<?php

namespace App\Services\SMS\Providers;

use App\Services\SMS\Contracts\SMSProviderInterface;
use Symfony\Component\Process\Process;

class GSMModemProvider implements SMSProviderInterface
{
    public function send(
        string $phone,
        string $message
    ): bool {

        $settings = system_settings();

        $message = preg_replace(
            '/[^\x20-\x7E]/',
            '',
            $message
        );

        $process = new Process([
            $settings->python_path,
            base_path('sms/send_sms.py'),
            $settings->port_com,
            $phone,
            $message,
        ]);

        $process->setTimeout(60);

        $process->run();

        if (!$process->isSuccessful()) {

            throw new \Exception(
                'GSM SEND FAILED: '
                . $process->getErrorOutput()
            );
        }

        return true;
    }
}
