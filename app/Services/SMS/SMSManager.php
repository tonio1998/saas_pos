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
    ): bool {

        $result = $this->provider
            ->send($phone, $message);

        if (!$result) {

            throw new \Exception(
                'SMS provider returned FALSE.'
            );
        }

        $this->incrementSent();

        return true;
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
