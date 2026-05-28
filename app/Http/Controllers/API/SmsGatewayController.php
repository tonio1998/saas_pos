<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\SMS\SMSManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SmsGatewayController extends Controller
{
    public function process(Request $request)
    {
        if (
            $request->header('X-API-KEY')
            !== env('SMS_API_KEY')
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent Multiple Worker Instances
        |--------------------------------------------------------------------------
        */

        if (
            Cache::has('sms_process_running')
        ) {

            return response()->json([
                'success' => true,
                'triggered' => false,
                'running' => true,
                'message' => 'SMS processor is already running.',
            ]);
        }

        Cache::put(
            'sms_process_running',
            true
        );

        try {

            Artisan::call('sms:process');

            return response()->json([
                'success' => true,
                'triggered' => true,
                'running' => true,
                'message' => 'SMS processor started successfully.',
            ]);

        } catch (\Throwable $e) {

            Cache::forget(
                'sms_process_running'
            );

            return response()->json([
                'success' => false,
                'triggered' => false,
                'running' => false,
                'message' => 'Failed to start SMS processor.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function send(Request $request)
    {
        if (
            $request->header('X-API-KEY')
            !== env('SMS_API_KEY')
        ) {

            return response()->json([
                'success' => false,
                'remark' => 'unauthorized',
                'message' => 'Unauthorized access.',
            ], 403);
        }

        $request->validate([
            'number' => [
                'required',
                'string',
            ],

            'message' => [
                'required',
                'string',
            ],
        ]);

        $portName  = fopen(
            '\\\\.\\COM5',
            'w+'
        );

        if (!$portName) {

            return response()->json([
                'success' => false,
                'remark' => 'failed',
                'message' => 'COM port not configured.',
            ], 500);
        }

        try {

            $phone = preg_replace(
                '/[^0-9]/',
                '',
                $request->number
            );

            if (
                str_starts_with($phone, '09')
            ) {

                $phone = '63'
                    . substr($phone, 1);
            }

            $message = trim(
                $request->message
            );

            $message = substr(
                $message,
                0,
                120
            );

            $port = fopen(
                $portName,
                'w+'
            );

            if (!$port) {

                return response()->json([
                    'success' => false,
                    'remark' => 'failed',
                    'message' => 'Unable to open COM port.',
                    'debug' => [
                        'port' => $portName,
                    ],
                ], 500);
            }

            stream_set_blocking(
                $port,
                true
            );

            fwrite($port, "AT\r");

            sleep(1);

            $atResponse = fread(
                $port,
                128
            );

            fwrite($port, "AT+CMGF=1\r");

            sleep(1);

            $textModeResponse = fread(
                $port,
                128
            );

            fwrite(
                $port,
                "AT+CMGS=\"{$phone}\"\r"
            );

            sleep(2);

            $recipientResponse = fread(
                $port,
                128
            );

            fwrite(
                $port,
                $message
            );

            fwrite(
                $port,
                chr(26)
            );

            sleep(5);

            $finalResponse = fread(
                $port,
                1024
            );

            fclose($port);

            return response()->json([

                'success' => str_contains(
                        $finalResponse,
                        'OK'
                    ) || str_contains(
                        $finalResponse,
                        '+CMGS'
                    ),

                'remark' => str_contains(
                    $finalResponse,
                    '+CMGS'
                )
                    ? 'sent'
                    : 'failed',

                'message' => str_contains(
                    $finalResponse,
                    '+CMGS'
                )
                    ? 'SMS sent successfully.'
                    : 'SMS sending failed.',

                'debug' => [

                    'port' => $portName,

                    'phone' => $phone,

                    'processed_message' => $message,

                    'responses' => [

                        'AT' => $atResponse,

                        'TEXT_MODE' => $textModeResponse,

                        'RECIPIENT' => $recipientResponse,

                        'FINAL' => $finalResponse,
                    ],
                ],

            ], str_contains(
                $finalResponse,
                '+CMGS'
            ) ? 200 : 500);

        } catch (\Throwable $e) {

            return response()->json([

                'success' => false,

                'remark' => 'failed',

                'message' => 'Unexpected GSM exception.',

                'error' => $e->getMessage(),

                'debug' => [
                    'port' => $portName,
                ],

            ], 500);
        }
    }
}
