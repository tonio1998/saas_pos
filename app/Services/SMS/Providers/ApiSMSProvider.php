<?php

namespace App\Services\SMS\Providers;

use App\Services\SMS\Contracts\SMSProviderInterface;
use Illuminate\Support\Facades\Http;

class ApiSMSProvider implements SMSProviderInterface
{
    public function send(
        string $phone,
        string $message
    ): bool {

        $settings = system_settings();

        $response = Http::timeout(30)

            ->post(
                $settings->sms_api_url,
                [
                    'api_key' => $settings->sms_api_key,
                    'device_id' => $settings->sms_api_device_id,
                    'phone' => $phone,
                    'message' => $message,
                ]
            );

        if (!$response->successful()) {

            throw new \Exception(
                'API SMS FAILED: '
                . $response->status()
                . ' | '
                . $response->body()
            );
        }

        return true;
    }
}
