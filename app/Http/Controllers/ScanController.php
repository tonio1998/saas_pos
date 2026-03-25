<?php

namespace App\Http\Controllers;

use App\Models\ScanLogs;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ScanController extends Controller
{
    public function scan(Request $request)
    {
        try {

            $input = trim($request->code);

            if (!$input) {
                return response()->json([
                    'status' => 'denied',
                    'name' => 'Invalid Input',
                    'role' => '',
                    'photo' => asset('images/avatar.png'),
                    'time' => now()->format('H:i')
                ], 400);
            }

            $user = null;
//            dd(ctype_digit($input));
            if (ctype_digit($input)) {

                $user = User::where('id', $input)
                    ->orWhere('qr_code', $input)
                    ->first();
//                dd($user);

            } else {

                $user = User::where('nfc_code', $input)
                    ->orWhere('qr_code', $input)
                    ->first();
            }

            if (!$user) {
                return response()->json([
                    'status' => 'denied',
                    'name' => 'User Not Found',
                    'role' => '',
                    'photo' => asset('images/avatar.png'),
                    'time' => now()->format('H:i')
                ]);
            }

            $lastLog = ScanLogs::where('UserID', $user->id)
                ->latest()
                ->first();

            $mode = ($lastLog && $lastLog->Mode == 1) ? 0 : 1;

//            dd($mode);

            $verificationCode = null;

            do {
                $verificationCode = strtoupper(Str::random(16));
            } while (
                ScanLogs::where('VerificationCode', $verificationCode)->exists()
            );


            $logs = new ScanLogs();
            $logs->UserID = $user->id;
            $logs->Mode = $mode;
            $logs->VerificationCode = $verificationCode;
            $logs->created_by = $user->id;
            $logs->updated_by = $user->id;
            $logs->status = 'active';
            $logs->archived = 0;
            $logs->save();

            return response()->json([
                'status' => $mode == 1 ? 'in' : 'out',
                'name' => $user->name,
                'role' => $user->role ?? 'Student',
                'photo' => $user->filepath
                    ? asset('storage/'.$user->filepath)
                    : asset('images/avatar.png'),
                'time' => now()->format('H:i')
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'status' => 'denied',
                'name' => 'System Error',
                'role' => '',
                'photo' => asset('images/avatar.png'),
                'time' => now()->format('H:i')
            ], 500);
        }
    }
}
