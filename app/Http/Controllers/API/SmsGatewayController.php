<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SmsGatewayController extends Controller
{
    public function process(Request $request)
    {
        if (
            $request->header('X-API-KEY')
            !== env('SMS_API_KEY')
        ) {
            abort(403);
        }

        Artisan::call('sms:process');

        return response()->json([
            'success' => true,
            'message' => 'SMS processing triggered.',
        ]);
    }
}
