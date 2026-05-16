<?php

namespace App\Console\Commands;

use App\Models\Settings;
use App\Models\SmsQueuingModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class SendQueuedSmsss extends Command
{
    protected $signature = 'sms:sendssss';

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

                        $provider = strtolower(
                            $schoolSettings->sms_provider ?? 'api'
                        );

                        $output = '';

                        $errorOutput = '';

                        $isSuccessful = false;
                        $this->line($provider);
                        if ($provider === 'api') {

                            if (
                                empty($schoolSettings->sms_api_url) ||
                                empty($schoolSettings->sms_api_key)
                            ) {

                                throw new \Exception(
                                    'SMS API configuration is incomplete.'
                                );
                            }

                            $payload = [
                                'apikey' => $schoolSettings->sms_api_key,
                                'recipient' => $sms->PhoneNumber,
                                'message' => $sms->Message,
                            ];

                            if (!empty($schoolSettings->sms_api_device_id)) {

                                $payload['device_id'] =
                                    $schoolSettings->sms_api_device_id;
                            }

                            $this->warn('================ API REQUEST ================');

                            $this->line('URL: ' . $schoolSettings->sms_api_url);

                            $this->line('Payload:');

                            $this->line(json_encode(
                                $payload,
                                JSON_PRETTY_PRINT
                            ));

                            Log::info('SMS API REQUEST', [
                                'url' => $schoolSettings->sms_api_url,
                                'payload' => $payload,
                            ]);

                            $response = Http::timeout(30)
                                ->acceptJson()
                                ->asJson()
                                ->post(
                                    $schoolSettings->sms_api_url,
                                    $payload
                                );

                            $output = trim($response->body());

                            $errorOutput = $response->successful()
                                ? ''
                                : $response->body();

                            $isSuccessful = $response->successful();

                            $this->warn('================ API RESPONSE ================');

                            $this->line('HTTP Status: ' . $response->status());

                            $this->line('Success: ' . (
                                $response->successful()
                                    ? 'YES'
                                    : 'NO'
                                ));

                            $this->line('Headers:');

                            $this->line(json_encode(
                                $response->headers(),
                                JSON_PRETTY_PRINT
                            ));

                            $this->line('Body:');

                            $this->line($response->body());

                            Log::info('SMS API RESPONSE', [
                                'status' => $response->status(),
                                'success' => $response->successful(),
                                'headers' => $response->headers(),
                                'body' => $response->body(),
                            ]);
                        } else {

                            if (empty($schoolSettings->python_path)) {

                                throw new \Exception(
                                    'Python path is empty.'
                                );
                            }

                            if (empty($schoolSettings->port_com)) {

                                throw new \Exception(
                                    'COM Port is empty.'
                                );
                            }

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

                            $errorOutput = trim(
                                $process->getErrorOutput()
                            );

                            $isSuccessful = $process->isSuccessful();
                        }

                        if (
                            !$isSuccessful ||
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

                        $sms->total_sent =
                            ($sms->total_sent ?? 0) + 1;

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

                        $schoolSettings->sms_failed_count =
                            ($schoolSettings->sms_failed_count ?? 0) + 1;

                        $schoolSettings->sms_last_failed_at = now();

                        $schoolSettings->save();

                        Cache::forget('school_settings');

                        $message = $e->getMessage();

                        $this->error($message);

                        Log::error($message);
                    }

                    sleep(2);
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
