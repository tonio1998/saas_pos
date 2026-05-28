<?php

namespace App\Services\SMS;

use App\Models\SmsQueuingModel;
use App\Models\SystemSetting;
use App\Services\SMS\Providers\ApiSMSProvider;
use App\Services\SMS\Providers\GSMModemProvider;
use Illuminate\Support\Facades\Log;

class SMSManager
{
    protected $provider;

    public function __construct()
    {
        $settings = system_settings();

        $provider = $settings->sms_provider ?? 'api';

        $this->provider = match ($provider) {

            'gsm' => app(GSMModemProvider::class),

            default => app(ApiSMSProvider::class),
        };
    }

    public function queue(
        string $phone,
        string $message,
        ?int $schoolId = null
    ): void {

        SmsQueuingModel::create([

            'school_id' => $schoolId,

            'PhoneNumber' => $phone,

            'Message' => $message,

            'remark' => 'pending',

            'status' => 'active',

            'archived' => 0,

            'created_by' => 0,

            'updated_by' => 0,
        ]);
    }

    public function send(
        string $phone,
        string $message
    ): array {

        $result = $this->provider
            ->send($phone, $message);

        if (
            !($result['success'] ?? false)
        ) {

            $this->markFailed();

            Log::error('SMS SEND FAILED', [
                'phone' => $phone,
                'message' => $message,
                'error' => $result['error'] ?? null,
                'provider_result' => $result,
            ]);

            return [
                'success' => false,
                'remark' => 'failed',
                'message' => $result['message']
                    ?? 'SMS sending failed.',
                'error' => $result['error']
                    ?? null,
            ];
        }

        $this->incrementSent();

        Log::info('SMS SEND SUCCESS', [
            'phone' => $phone,
            'message' => $message,
        ]);

        return [
            'success' => true,
            'remark' => 'sent',
            'message' => $result['message']
                ?? 'SMS sent successfully.',
        ];
    }

    protected function incrementSent(): void
    {
        SystemSetting::query()
            ->first()
            ?->increment('total_sent');
    }

    protected function markFailed(): void
    {
        $settings = SystemSetting::query()
            ->first();

        if (!$settings) {
            return;
        }

        $settings->increment('sms_failed_count');

        $settings->update([
            'sms_last_failed_at' => now(),
        ]);
    }
}
