<?php

namespace App\Console\Commands;

use App\Models\Settings;
use App\Models\SmsQueuingModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class SendQueuedSms extends Command
{
    protected $signature = 'sms:send';

    protected $description = 'Send queued SMS messages';

    public function handle(): int
    {
        $this->info('SMS Worker Started...');

        Log::info('SMS Worker Started...');

        while (true) {

            try {

                $message = 'SMS Scheduler Running: ' . now();

                $this->info($message);

                Log::info($message);

                Cache::forget('school_settings');

                $schoolSettings = Cache::rememberForever(
                    'school_settings',
                    function () {

                        return Settings::query()

                            ->with([
                                'principal',
                                'registrar'
                            ])

                            ->first();
                    }
                );

                if (!$schoolSettings) {

                    $message = 'School settings not found.';

                    $this->error($message);

                    Log::error($message);

                    sleep(10);

                    continue;
                }

                if (empty($schoolSettings->python_path)) {

                    $message = 'Python path is empty.';

                    $this->error($message);

                    Log::error($message);

                    sleep(10);

                    continue;
                }

                $queuedMessages = SmsQueuingModel::query()
                    ->where(function ($query) {
                        $query
                            ->where('remark', '!=', 'sent')
                            ->orWhereNull('remark');
                    })
                    ->limit(25)
                    ->get();

                $message = 'Queued SMS Count: ' . $queuedMessages->count();

                $this->line($message);

                Log::info($message);

                if ($queuedMessages->isEmpty()) {

                    $message = 'No pending SMS found.';

                    $this->warn($message);

                    Log::warning($message);

                    sleep(10);

                    continue;
                }

                foreach ($queuedMessages as $sms) {

                    try {

                        $sms->remark = 'processing';

                        $sms->save();

                        $message = "Sending SMS to {$sms->PhoneNumber}";

                        $this->line($message);

                        Log::info($message);

                        $process = new Process([
                            $schoolSettings->python_path,
                            base_path('sms/send_sms.py'),
                            $sms->PhoneNumber,
                            $sms->Message,
                            $schoolSettings->port_com,
                        ]);

                        $process->setTimeout(15);

                        $process->run();

                        $output = trim($process->getOutput());

                        $errorOutput = trim($process->getErrorOutput());

                        if (
                            !$process->isSuccessful() ||
                            str_contains(strtolower($errorOutput), 'error') ||
                            str_contains(strtolower($output), 'error')
                        ) {

                            $sms->remark = 'failed';

                            $sms->save();

                            $schoolSettings->sms_failed_count =
                                ($schoolSettings->sms_failed_count ?? 0) + 1;

                            $schoolSettings->sms_last_failed_at = now();

                            if (
                                str_contains(strtolower($errorOutput), 'cms error') ||
                                str_contains(strtolower($errorOutput), 'no carrier') ||
                                str_contains(strtolower($errorOutput), 'credit') ||
                                str_contains(strtolower($errorOutput), 'balance') ||
                                str_contains(strtolower($output), 'credit') ||
                                str_contains(strtolower($output), 'balance')
                            ) {

                                $schoolSettings->sms_low_balance = 1;
                            }

                            if ($schoolSettings->sms_failed_count >= 5) {

                                $schoolSettings->sms_low_balance = 1;
                            }

                            $schoolSettings->save();

                            Cache::forget('school_settings');

                            $message = "SMS FAILED to {$sms->PhoneNumber}: {$errorOutput}";

                            $this->error($message);

                            Log::error($message);

                            continue;
                        }

                        $sms->remark = 'sent';
                        $sms->total_sent = $sms->total_sent + 1;
                        $sms->save();

                        $schoolSettings->sms_failed_count = 0;

                        $schoolSettings->sms_low_balance = 0;

                        $schoolSettings->save();

                        Cache::forget('school_settings');

                        $message = "SMS sent to {$sms->PhoneNumber}";

                        $this->info($message);

                        Log::info($message);

                    } catch (\Throwable $e) {

                        $sms->remark = 'failed';

                        $sms->save();

                        $message = $e->getMessage();

                        $this->error($message);

                        Log::error($message);
                    }
                }

                $message = 'SMS Scheduler Finished.';

                $this->info($message);

                Log::info($message);

            } catch (\Throwable $e) {

                $message = $e->getMessage();

                $this->error($message);

                Log::error($message);
            }

            sleep(10);
        }

        return self::SUCCESS;
    }
}
