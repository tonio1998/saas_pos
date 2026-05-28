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

        try {

            $result = app(
                SMSManager::class
            )->send(
                $request->number,
                $request->message
            );

            return response()->json([
                'success' => $result['success'] ?? false,
                'remark' => $result['remark'] ?? null,
                'message' => $result['message'] ?? null,
                'error' => $result['error'] ?? null,
                'result' => $result,
                'debug' => [
                    'provider' => system_settings()->sms_provider,
                    'python_path' => system_settings()->python_path,
                    'com_port' => system_settings()->port_com,
                    'timestamp' => now(),
                ],
            ], ($result['success'] ?? false) ? 200 : 500);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => 'SMS sending failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
