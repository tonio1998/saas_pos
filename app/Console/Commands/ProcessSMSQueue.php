<?php

namespace App\Console\Commands;

use App\Models\SmsQueuingModel;
use App\Services\SMS\Providers\GSMModemProvider;
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
            'Bulk SMS Queue Worker Started...'
        );

        while (true) {

            try {

                $messages = SmsQueuingModel::query()

                    ->where(
                        'remark',
                        '!=',
                        'sent'
                    )

                    ->where(
                        'status',
                        'active'
                    )

                    ->orderBy('id')

                    ->limit(20)

                    ->get();

                if (
                    $messages->isEmpty()
                ) {

                    $this->line(
                        '['
                        . now()
                        . '] No pending SMS.'
                    );

                    sleep(5);

                    continue;
                }

                $this->newLine();

                $this->info(
                    'Processing '
                    . $messages->count()
                    . ' SMS message(s)...'
                );

                foreach (
                    $messages
                    as $sms
                ) {

                    $sms->update([

                        'remark'
                        => 'processing',
                    ]);
                }

                $results = app(
                    GSMModemProvider::class
                )->bulkSend(
                    $messages
                );

                foreach (
                    $results
                    as $result
                ) {

                    $sms =
                        $messages
                            ->firstWhere(
                                'id',
                                $result['id']
                            );

                    if (!$sms) {
                        continue;
                    }

                    if (
                        $result['success']
                    ) {

                        $sms->update([

                            'remark'
                            => 'sent',

                            'error_message'
                            => null,
                        ]);

                        $this->info(

                            'SMS SENT: '

                            . $sms->PhoneNumber
                        );

                    } else {

                        $sms->update([

                            'remark'
                            => 'failed',

                            'error_message'
                            => $result['error'],
                        ]);

                        $this->error(

                            'FAILED: '

                            . $sms->PhoneNumber

                            . ' | '

                            . $result['error']
                        );
                    }
                }

                usleep(300000);

            } catch (\Throwable $e) {

                report($e);

                $this->newLine();

                $this->error(
                    'QUEUE LOOP ERROR'
                );

                $this->line(
                    $e->getMessage()
                );

                sleep(3);
            }
        }

        return self::SUCCESS;
    }
}
