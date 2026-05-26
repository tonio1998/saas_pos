<?php

namespace App\Console\Commands;

use App\Models\SmsQueuingModel;
use App\Services\SMS\SMSManager;
use Illuminate\Console\Command;

class ProcessSMSQueue extends Command
{
    protected $signature =
        'sms:process';

    protected $description =
        'Process pending SMS queue';

    public function handle(): int
    {
        $this->info(
            'SMS Queue Worker Started...'
        );

        while (true) {

            try {

                $messages = SmsQueuingModel::query()

                    ->where('remark', '!=', 'sent')

                    ->where('status', 'active')

                    ->limit(20)

                    ->get();

                if ($messages->isEmpty()) {

                    $this->line(
                        '['
                        . now()
                        . '] No pending SMS.'
                    );

                    sleep(5);

                    continue;
                }

                foreach ($messages as $sms) {

                    try {

                        $this->newLine();

                        $this->info(
                            'Processing SMS ID: '
                            . $sms->id
                        );

                        $this->line(
                            'Phone: '
                            . $sms->PhoneNumber
                        );

                        $this->line(
                            'Message: '
                            . $sms->Message
                        );

                        $sms->update([
                            'remark' => 'processing',
                        ]);

                        $result = app(
                            SMSManager::class
                        )->send(
                            $sms->PhoneNumber,
                            $sms->Message
                        );

                        if ($result) {

                            $sms->update([
                                'remark' => 'sent',
                            ]);

                            $this->info(
                                'SMS SENT SUCCESSFULLY'
                            );

                        } else {

                            $sms->update([
                                'remark' => 'pending',
                            ]);

                            $this->error(
                                'SMS FAILED'
                            );
                        }

                    } catch (\Throwable $e) {

                        report($e);

                        /*
                        |--------------------------------------------------------------------------
                        | IMPORTANT
                        |--------------------------------------------------------------------------
                        |
                        | Do NOT permanently fail immediately.
                        | GSM issues are usually temporary.
                        |
                        */

                        $sms->update([
                            'remark' => 'pending',
                        ]);

                        $this->newLine();

                        $this->error(
                            'SMS PROCESSING ERROR'
                        );

                        $this->line(
                            'SMS ID: '
                            . $sms->id
                        );

                        $this->line(
                            'Phone: '
                            . $sms->PhoneNumber
                        );

                        $this->line(
                            'Message: '
                            . $sms->Message
                        );

                        $this->newLine();

                        $errorMessage =
                            $e->getMessage();

                        $friendlyError =
                            'Unknown GSM modem error.';

                        if (
                            str_contains(
                                $errorMessage,
                                'No network signal'
                            )
                        ) {

                            $friendlyError =
                                'No network signal detected.';
                        }

                        elseif (
                            str_contains(
                                $errorMessage,
                                'Weak signal'
                            )
                        ) {

                            $friendlyError =
                                'Weak GSM signal.';
                        }

                        elseif (
                            str_contains(
                                $errorMessage,
                                'SIM card is locked'
                            )
                        ) {

                            $friendlyError =
                                'SIM card locked or requires PIN.';
                        }

                        elseif (
                            str_contains(
                                $errorMessage,
                                'SMS Center Number'
                            )
                        ) {

                            $friendlyError =
                                'Invalid SMSC configuration.';
                        }

                        elseif (
                            str_contains(
                                $errorMessage,
                                'Modem not responding'
                            )
                        ) {

                            $friendlyError =
                                'Modem disconnected or frozen.';
                        }

                        elseif (
                            str_contains(
                                $errorMessage,
                                'Serial port error'
                            )
                        ) {

                            $friendlyError =
                                'COM port busy/unavailable.';
                        }

                        elseif (
                            str_contains(
                                $errorMessage,
                                'no load'
                            )
                        ) {

                            $friendlyError =
                                'SIM may have insufficient load.';
                        }

                        elseif (
                            str_contains(
                                $errorMessage,
                                'network rejection'
                            )
                        ) {

                            $friendlyError =
                                'Network rejected SMS.';
                        }

                        $this->error(
                            'Friendly Error: '
                            . $friendlyError
                        );

                        $this->newLine();

                        $this->line(
                            'Raw Error Message:'
                        );

                        $this->line(
                            $errorMessage
                        );

                        $this->newLine();

                        $this->line(
                            'File: '
                            . $e->getFile()
                        );

                        $this->line(
                            'Line: '
                            . $e->getLine()
                        );

                        $this->newLine();

                        /*
                        |--------------------------------------------------------------------------
                        | GSM Recovery Cooldown
                        |--------------------------------------------------------------------------
                        */

                        sleep(10);

                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | GSM Modem Cooldown
                    |--------------------------------------------------------------------------
                    */

                    sleep(5);
                }

            } catch (\Throwable $e) {

                report($e);

                $this->newLine();

                $this->error(
                    'QUEUE LOOP ERROR'
                );

                $this->line(
                    $e->getMessage()
                );

                /*
                |--------------------------------------------------------------------------
                | Prevent crash loop
                |--------------------------------------------------------------------------
                */

                sleep(10);
            }
        }

        return self::SUCCESS;
    }
}
