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
                    'role' => [],
                    'photo' => asset('images/avatar.png'),
                    'time' => now()->format('H:i A')
                ], 400);
            }

            $query = User::with([
                'roles',
                'studentInfo.guardian',
                'teacherInfo',
                'guardianInfo'
            ]);

            if (ctype_digit($input)) {
                $query->where('qr_code', $input)
                    ->orWhere('id', $input);
            } else {
                $query->where('nfc_code', $input)
                    ->orWhere('qr_code', $input);
            }

            $user = $query->first();

            if (!$user) {
                return response()->json([
                    'status' => 'denied',
                    'name' => 'User Not Found',
                    'role' => [],
                    'photo' => asset('images/avatar.png'),
                    'time' => now()->format('H:i A')
                ]);
            }

            $roles = $user->getRoleNames();


            $lastLog = ScanLogs::where('UserID', $user->id)
                ->latest()
                ->first();

            $mode = ($lastLog && $lastLog->Mode == 1) ? 0 : 1;

            do {
                $verificationCode = strtoupper(Str::random(16));
            } while (
                ScanLogs::where('VerificationCode', $verificationCode)->exists()
            );

            ScanLogs::create([
                'UserID' => $user->id,
                'Mode' => $mode,
                'VerificationCode' => $verificationCode,
                'created_by' => $user->id ?? 0,
                'updated_by' => $user->id ?? 0,
                'status' => 'active',
                'archived' => 0
            ]);

            $message = $user->name . ' just '
                . ($mode == 1 ? 'entered' : 'left') . ' '
                . env('SCHOOL_NAME') . ' @ '
                . now()->format('M d, Y h:i:s A')
                . ". Code: $verificationCode";

            $phoneNumbers = [];

            if ($roles->contains('students')) {
                $phoneNumbers[] = $user->studentInfo?->guardian?->PhoneNumber;
            }

            if ($roles->contains('parents')) {
                $phoneNumbers[] = $user->guardianInfo?->PhoneNumber;
            }

            if ($roles->contains('teachers')) {
                $phoneNumbers[] = $user->teacherInfo?->PhoneNumber ?? null;
            }

            foreach (array_unique(array_filter($phoneNumbers)) as $number) {
                if (strlen($number) >= 10) {
                    queueSMSSend($number, $message);
                }
            }

            return response()->json([
                'status' => $mode == 1 ? 'in' : 'out',
                'name' => $user->name,
                'role' => $roles->map(fn($r) => ucfirst($r))->values(),
                'photo' => $user->filepath
                    ? asset('storage/' . $user->filepath)
                    : asset('images/avatar.png'),
                'time' => now()->format('H:i A')
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'status' => 'denied',
                'name' => 'System Error',
                'role' => [],
                'photo' => asset('images/avatar.png'),
                'time' => now()->format('H:i A')
            ], 500);
        }
    }
}
