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

        if (!$settings->python_path) {

            throw new \Exception(
                'Python path not configured.'
            );
        }

        if (!$settings->port_com) {

            throw new \Exception(
                'COM port not configured.'
            );
        }

        $pythonScript = base_path('sms/send_sms.py');

        if (!file_exists($pythonScript)) {

            throw new \Exception(
                'send_sms.py not found.'
            );
        }

        $message = iconv(
            'UTF-8',
            'ASCII//TRANSLIT//IGNORE',
            $message
        );

//        $message = str_replace(
//            ["\r", "\n"],
//            ' ',
//            $message
//        );
//
//        $message = preg_replace(
//            '/[^A-Za-z0-9\s\.\,\-\@\:\(\)]/',
//            '',
//            $message
//        );
//
//        $message = preg_replace(
//            '/\s+/',
//            ' ',
//            $message
//        );

        $message = trim($message);

        $message = substr(
            $message,
            0,
            120
        );

        $process = new Process([
            $settings->python_path,
            $pythonScript,
            $phone,
            $message,
            $settings->port_com,
        ]);

        $process->setTimeout(60);

        $process->run();

        if (!$process->isSuccessful()) {

            $errorOutput = trim(
                $process->getErrorOutput()
            );

            $standardOutput = trim(
                $process->getOutput()
            );

//            throw new \Exception(
//                "GSM SEND FAILED\n\n"
//                . "PORT: "
//                . $settings->port_com
//                . "\n\n"
//                . "PHONE: "
//                . $phone
//                . "\n\n"
//                . "MESSAGE: "
//                . $message
//                . "\n\n"
//                . "EXIT CODE: "
//                . $process->getExitCode()
//                . "\n\n"
//                . "ERROR OUTPUT:\n"
//                . ($errorOutput ?: 'EMPTY')
//                . "\n\n"
//                . "STANDARD OUTPUT:\n"
//                . ($standardOutput ?: 'EMPTY')
//            );
        }

        return true;
    }
}
