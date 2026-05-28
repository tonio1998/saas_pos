<?php

namespace App\Services\SMS;

use App\Models\SmsQueuingModel;
use App\Models\SystemSetting;
use App\Services\SMS\Providers\ApiSMSProvider;
use App\Services\SMS\Providers\GSMModemProvider;
use Throwable;

class SMSManager
{
    protected $provider;
    protected ?SystemSetting $settings = null;
    private const MAX_RETRY = 2;
    private const RETRY_DELAY_US = 500000;

    public function __construct()
    {
        $this->settings = system_settings();

        $provider = $this->settings?->sms_provider
            ?? 'api';

        $this->provider = match ($provider) {

            'gsm' => app(
                GSMModemProvider::class
            ),

            default => app(
                ApiSMSProvider::class
            ),
        };
    }

    public function queue(
        string $phone,
        string $message,
        ?int $schoolId = null
    ): void {

        SmsQueuingModel::create([

            'school_id'
            => $schoolId,

            'PhoneNumber'
            => trim($phone),

            'Message'
            => trim($message),

            'remark'
            => 'pending',

            'status'
            => 'active',

            'archived'
            => 0,

            'created_by'
            => 0,

            'updated_by'
            => 0,
        ]);
    }

    public function send(
        SmsQueuingModel $sms
    ): bool {

        $lastException = null;

        $sms->increment(
            'attempt_count'
        );

        $sms->update([

            'last_attempt_at'
            => now(),

            'remark'
            => 'processing',
        ]);

        for (
            $attempt = 1;
            $attempt <= self::MAX_RETRY;
            $attempt++
        ) {

            try {

                $result = $this->provider
                    ->send(
                        $sms->PhoneNumber,
                        trim(
                            $sms->Message
                        )
                    );

                if (!$result) {

                    throw new \Exception(
                        'SMS provider returned FALSE.'
                    );
                }

                $this->settings->update([

                    'remark'=> 'sent',

                    'error_message'
                    => null,
                ]);

                $this->markSuccess();

                return true;

            } catch (Throwable $e) {

                $lastException = $e;

                report($e);

                $friendlyError =
                    $this->friendlyError(
                        $e->getMessage()
                    );

                $this->settings->update([

                    'remark'
                    => 'failed',

                    'error_message'
                    => $friendlyError,
                ]);

                $this->markFailed(
                    $friendlyError
                );

                if (
                    $attempt
                    < self::MAX_RETRY
                ) {

                    usleep(
                        self::RETRY_DELAY_US
                    );
                }
            }
        }

        throw new \Exception(
            $lastException?->getMessage()
            ?? 'SMS sending failed.'
        );
    }

    protected function friendlyError(
        string $message
    ): string {

        $message = strtolower(
            $message
        );

        return match (true) {

            str_contains(
                $message,
                'weak signal'
            ) => 'Weak GSM signal',

            str_contains(
                $message,
                'network registration'
            ) => 'Network registration failed',

            str_contains(
                $message,
                'modem not responding'
            ) => 'Modem disconnected',

            str_contains(
                $message,
                'serial error'
            ) => 'COM port unavailable',

            str_contains(
                $message,
                'timeout'
            ) => 'SMS timeout',

            str_contains(
                $message,
                'recipient rejected'
            ) => 'Recipient rejected',

            str_contains(
                $message,
                'sim'
            ) => 'SIM card issue',

            default => 'Unknown GSM error',
        };
    }

    protected function markSuccess(): void
    {
        if (!$this->settings) {
            return;
        }

        $this->settings->increment(
            'total_sent'
        );

        $this->settings->update([

            'sms_status'
            => 'online',

            'sms_signal_status'
            => 'good',

            'sms_last_error'
            => null,

            'sms_last_failed_at'
            => null,
        ]);
    }

    protected function markFailed(
        string $message
    ): void {

        if (!$this->settings) {
            return;
        }

        $signalStatus =
            $this->detectSignalStatus(
                $message
            );

        $this->settings->increment(
            'sms_failed_count'
        );

        $this->settings->update([

            'sms_status'
            => 'error',

            'sms_signal_status'
            => $signalStatus,

            'sms_last_error'
            => $message,

            'sms_last_failed_at'
            => now(),
        ]);
    }

    protected function detectSignalStatus(
        string $message
    ): string {

        $message = strtolower(
            $message
        );

        return match (true) {

            str_contains(
                $message,
                'weak signal'
            ) => 'weak',

            str_contains(
                $message,
                'network registration'
            ) => 'searching',

            str_contains(
                $message,
                'modem disconnected'
            ) => 'offline',

            str_contains(
                $message,
                'timeout'
            ) => 'slow',

            default => 'unknown',
        };
    }
}
