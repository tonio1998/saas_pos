<?php

namespace App\Services\SMS;

use App\Models\SmsQueuingModel;
use App\Models\SystemSetting;
use App\Services\SMS\Providers\ApiSMSProvider;
use App\Services\SMS\Providers\GSMModemProvider;
use App\Traits\TCommonFunctions;
use Throwable;

class SMSManager
{
    use TCommonFunctions;
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


        try {
            $sms = new SmsQueuingModel();
            $sms->school_id = $schoolId ?? 0;
            $sms->PhoneNumber = trim($phone);
            $sms->Message = trim($message);
            $this->setCommonFields($sms);
            $sms->save();
        } catch (\Throwable $e) {
            report($e);
        }

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

        if ($this->settings) {

            $this->settings->update([

                'sms_status'
                => 'processing',

                'remark'
                => 'Sending SMS...',
            ]);
        }

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

                $sms->update([

                    'remark'
                    => 'sent',

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

                $sms->update([

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

            'remark'
            => 'SMS operational',
        ]);
    }

    protected function markFailed(
        string $message
    ): void {

        if (!$this->settings) {
            return;
        }

        $this->settings->increment(
            'sms_failed_count'
        );

        $this->settings->update([

            'sms_status'
            => 'error',

            'sms_signal_status'
            => $this->detectSignalStatus(
                $message
            ),

            'sms_last_error'
            => $message,

            'sms_last_failed_at'
            => now(),

            'remark'
            => $message,
        ]);
    }

    protected function detectSignalStatus(
        string $message
    ): string {

        $message = strtolower(
            trim($message)
        );

        if (
            str_contains(
                $message,
                '+creg: 0,0'
            )
        ) {

            return 'offline';
        }

        if (
            str_contains(
                $message,
                '+creg: 0,2'
            )
        ) {

            return 'searching';
        }

        if (
            str_contains(
                $message,
                '+creg: 0,3'
            )
        ) {

            return 'denied';
        }

        if (
            str_contains(
                $message,
                'weak signal'
            )
        ) {

            return 'weak';
        }

        if (
            str_contains(
                $message,
                'timeout'
            )
        ) {

            return 'slow';
        }

        return 'unknown';
    }

    protected function friendlyError(
        string $message
    ): string {

        $message = strtolower(
            trim($message)
        );

        if (
            str_contains(
                $message,
                '+creg: 0,0'
            )
        ) {

            return
                'SIM not connected to GSM network';
        }

        if (
            str_contains(
                $message,
                '+creg: 0,2'
            )
        ) {

            return
                'Searching GSM network';
        }

        if (
            str_contains(
                $message,
                '+creg: 0,3'
            )
        ) {

            return
                'GSM network registration denied';
        }

        if (
            str_contains(
                $message,
                'weak signal'
            )
        ) {

            return
                'Weak GSM signal';
        }

        if (
            str_contains(
                $message,
                'network registration'
            )
        ) {

            return
                'Network registration failed';
        }

        if (
            str_contains(
                $message,
                'modem not responding'
            )
        ) {

            return
                'Modem disconnected';
        }

        if (
            str_contains(
                $message,
                'serial error'
            )
        ) {

            return
                'COM port unavailable';
        }

        if (
            str_contains(
                $message,
                'timeout'
            )
        ) {

            return
                'SMS timeout';
        }

        if (
            str_contains(
                $message,
                'recipient rejected'
            )
        ) {

            return
                'Recipient rejected';
        }

        if (
            str_contains(
                $message,
                'sim'
            )
        ) {

            return
                'SIM card issue';
        }

        return
            'Unknown GSM modem error';
    }
}
