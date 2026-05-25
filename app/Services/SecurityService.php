<?php

namespace App\Services;

use App\Models\LoginActivity;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class SecurityService
{
    public function logLogin(
        Request $request,
                $user,
        string $status = 'success'
    ): void {

        $agent = new Agent();

        $agent->setUserAgent(
            $request->userAgent()
        );

        $activity = LoginActivity::create([

            'user_id' => $user?->id,

            'session_id' => session()->getId(),

            'email' => $user?->email
                ?? $request->login,

            'ip_address' => $request->ip(),

            'user_agent' => $request->userAgent(),

            'device' => $agent->device(),

            'platform' => $agent->platform(),

            'browser' => $agent->browser(),

            'status' => $status,

            'logged_in_at' => now(),
        ]);

        app(SecurityDetectionService::class)
            ->detectFailedLogins(
                $request->ip()
            );

        if ($user) {

            app(SecurityDetectionService::class)
                ->detectMultipleSessions(
                    $user->id
                );
        }
    }

    public function logLogout(
        Request $request,
                $user
    ): void {

        LoginActivity::query()

            ->where(
                'user_id',
                $user->id
            )

            ->whereNull(
                'logged_out_at'
            )

            ->latest()

            ->first()

            ?->update([

                'logged_out_at' => now(),

                'status' => 'logout',
            ]);
    }
}
