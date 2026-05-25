<?php

namespace App\Console\Commands;

use App\Models\SmsQueuingModel;
use App\Services\SMS\SMSManager;
use Illuminate\Console\Command;

class ProcessSMSQueue extends Command
{
    protected $signature = 'sms:process';

    protected $description =
        'Process pending SMS queue';

    public function handle(): void
    {
        $messages = SmsQueuingModel::query()

            ->where('remark', '!=', 'sent')

            ->where('status', 'active')

            ->limit(20)

            ->get();

        if ($messages->isEmpty()) {

            $this->info('No pending SMS.');

            return;
        }

        foreach ($messages as $sms) {

            try {

                $this->line('');

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

                $result = app(SMSManager::class)
                    ->send(
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
                        'remark' => 'failed',
                    ]);

                    $this->error(
                        'SMS FAILED'
                    );
                }

            } catch (\Throwable $e) {

                report($e);

                $sms->update([
                    'remark' => 'failed',
                ]);

                $this->newLine();

                $this->error('SMS PROCESSING ERROR');

                $this->line(
                    'SMS ID: '
                    . $sms->id
                );

                $this->line(
                    'Phone: '
                    . $sms->PhoneNumber
                );

                $this->line(
                    'Error Message: '
                    . $e->getMessage()
                );

                $this->line(
                    'File: '
                    . $e->getFile()
                );

                $this->line(
                    'Line: '
                    . $e->getLine()
                );

                $this->newLine();

                $this->line(
                    $e->getTraceAsString()
                );

                $this->newLine();
            }
        }
    }
}
