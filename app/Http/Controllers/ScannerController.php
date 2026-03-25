<?php

namespace App\Http\Controllers;

use App\Models\SmsQueuingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class ScannerController extends Controller
{
    public function index(){
        return view('pages.scanner.index');
    }

    public function send(Request $request)
    {
        $queuedMessages = SmsQueuingModel::where('remark', null)->limit(25)->get();
        $msg = '';
        foreach ($queuedMessages as $sms) {
            $number = $sms->PhoneNumber;
            $message = $sms->Message;

            Log::info("Attempting to send SMS to: $number");

            $process = new Process([
                env('PYTHON_PATH'),
                base_path('sms/send_sms.py'),
                $number,
                $message,
                env('PORT_COM'),
            ]);

            $process->run();

            if (!$process->isSuccessful()) {
//                Log::error("SMS sending FAILED to $number: " . $process->getErrorOutput());
                $msg = $process->getErrorOutput();
                continue;
            }

//            Log::info("SMS sent successfully to $number: " . $process->getOutput());

//            $sms->remark = $sms->remark === 'resent' ? 'sent' : 'resent';
            $sms->remark = 'sent';
            $sms->save();
        }

        return response()->json([
            'result' => true,
            'message' => $msg,
        ]);
    }
}
