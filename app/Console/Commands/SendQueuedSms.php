<?php

namespace App\Console\Commands;

use App\Models\Settings;
use App\Models\SmsQueuingModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
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
                            trim($schoolSettings->sms_provider ?? 'api')
                        );

                        $output = '';

                        $errorOutput = '';

                        $isSuccessful = false;

                        $this->line('SMS Provider: ' . strtoupper($provider));

                        if ($provider === 'api') {

                            if (
                                empty($schoolSettings->sms_api_url) ||
                                empty($schoolSettings->sms_api_key)
                            ) {

                                throw new \Exception(
                                    'SMS API configuration is incomplete.'
                                );
                            }

                            $apiUrl = rtrim(
                                    trim($schoolSettings->sms_api_url),
                                    '/'
                                ) . '/';

                            $phoneNumber = trim($sms->PhoneNumber);

                            $payload = [
                                'apikey' => trim(
                                    $schoolSettings->sms_api_key
                                ),
                                'recipients' => $phoneNumber,
                                'message' => trim($sms->Message),
                            ];

                            $query = http_build_query($payload);

                            $finalUrl = $apiUrl . '?' . $query;

                            $this->warn(
                                '================ API REQUEST ================'
                            );

                            $this->line('URL:');

                            $this->line($finalUrl);

                            $this->line('');

                            $this->line('Payload:');

                            $this->line(json_encode(
                                $payload,
                                JSON_PRETTY_PRINT
                            ));

                            Log::info('SMS API REQUEST', [
                                'url' => $finalUrl,
                                'payload' => $payload,
                            ]);

                            $response = Http::timeout(30)
                                ->acceptJson()
                                ->get($finalUrl);

                            $output = trim(
                                $response->body()
                            );

                            $errorOutput = $response->successful()
                                ? ''
                                : trim($response->body());

                            $responseJson = [];

                            try {

                                $responseJson =
                                    $response->json() ?? [];

                            } catch (\Throwable $e) {

                                $responseJson = [];
                            }

                            $this->warn(
                                '================ API RESPONSE ================'
                            );

                            $this->line(
                                'HTTP Status: ' .
                                $response->status()
                            );

                            $this->line(
                                'Reason: ' .
                                $response->reason()
                            );

                            $this->line('');

                            $this->line('Headers:');

                            $this->line(json_encode(
                                $response->headers(),
                                JSON_PRETTY_PRINT
                            ));

                            $this->line('');

                            $this->line('Body Raw:');

                            $this->line($response->body());

                            $this->line('');

                            $this->line('Body JSON:');

                            $this->line(json_encode(
                                $responseJson,
                                JSON_PRETTY_PRINT
                            ));

                            Log::info('SMS API RESPONSE', [
                                'status_code' => $response->status(),
                                'reason' => $response->reason(),
                                'success' => $response->successful(),
                                'headers' => $response->headers(),
                                'body_raw' => $response->body(),
                                'body_json' => $responseJson,
                            ]);

                            $combinedResponse = strtolower(
                                json_encode($responseJson) .
                                ' ' .
                                $output .
                                ' ' .
                                $errorOutput
                            );

                            $isSuccessful =
                                $response->successful() &&
                                (
                                    ($responseJson['result']['error'] ?? 1) == 0
                                ) &&
                                (
                                    ($responseJson['result']['sent'] ?? '0') == '1'
                                );

                            $this->line('$isSuccessful: ' . $isSuccessful);
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

                            $this->warn(
                                '================ GSM REQUEST ================'
                            );

                            $this->line('Python Path:');

                            $this->line(
                                $schoolSettings->python_path
                            );

                            $this->line('');

                            $this->line('COM Port:');

                            $this->line(
                                $schoolSettings->port_com
                            );

                            $this->line('');

                            $this->line('Phone Number:');

                            $this->line($sms->PhoneNumber);

                            $process = new Process([
                                $schoolSettings->python_path,
                                base_path('sms/send_sms.py'),
                                $sms->PhoneNumber,
                                $sms->Message,
                                $schoolSettings->port_com,
                            ]);

                            $process->setTimeout(15);

                            $process->run();

                            $output = trim(
                                $process->getOutput()
                            );

                            $errorOutput = trim(
                                $process->getErrorOutput()
                            );

                            $isSuccessful =
                                $process->isSuccessful();

                            $this->warn(
                                '================ GSM RESPONSE ================'
                            );

                            $this->line(
                                'Success: ' . (
                                $isSuccessful
                                    ? 'YES'
                                    : 'NO'
                                )
                            );

                            $this->line('');

                            $this->line('Output:');

                            $this->line($output);

                            $this->line('');

                            $this->line('Error Output:');

                            $this->line($errorOutput);

                            Log::info('SMS GSM RESPONSE', [
                                'success' => $isSuccessful,
                                'output' => $output,
                                'error_output' => $errorOutput,
                            ]);
                        }

                        if (!$isSuccessful) {

                            $sms->remark = 'failed';

                            $sms->save();

                            $schoolSettings->sms_failed_count =
                                ($schoolSettings->sms_failed_count ?? 0) + 1;

                            $schoolSettings->sms_last_failed_at =
                                now();
                            $schoolSettings->sms_low_balance = 1;
                            $schoolSettings->save();

                            Cache::forget('school_settings');

                            $message =
                                "SMS FAILED to {$sms->PhoneNumber}";

                            $this->error($message);

                            $this->error($output);

                            $this->error($errorOutput);

                            Log::error($message, [
                                'output' => $output,
                                'error_output' => $errorOutput,
                            ]);

                            continue;
                        }

                        $sms->remark = 'sent';
                        $sms->save();

                        $schoolSettings->total_sent =
                            ($schoolSettings->total_sent ?? 0) + 1;

                        $schoolSettings->save();

                        $schoolSettings->sms_failed_count = 0;

                        $schoolSettings->sms_low_balance = 0;

                        $schoolSettings->sms_last_failed_at =
                            null;

                        $schoolSettings->save();

                        Cache::forget('school_settings');

                        $message =
                            "SMS SENT to {$sms->PhoneNumber}";

                        $this->info($message);

                        Log::info($message);

                    } catch (\Throwable $e) {

                        $sms->remark = 'failed';

                        $sms->save();

                        $schoolSettings->sms_failed_count =
                            ($schoolSettings->sms_failed_count ?? 0) + 1;

                        $schoolSettings->sms_last_failed_at =
                            now();

                        $schoolSettings->save();

                        Cache::forget('school_settings');

                        $message = $e->getMessage();

                        $this->error($message);

                        Log::error($message, [
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }

                    sleep(2);
                }

                $message = 'SMS Scheduler Finished.';

                $this->info($message);

                Log::info($message);

            } catch (\Throwable $e) {

                $message = $e->getMessage();

                $this->error($message);

                Log::error($message, [
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            sleep(10);
        }

        return self::SUCCESS;
    }
}
