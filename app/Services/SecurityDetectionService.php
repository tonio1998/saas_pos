<?php

namespace App\Services;

use App\Models\LoginActivity;
use App\Models\SuspiciousActivity;

class SecurityDetectionService
{
    public function detectFailedLogins(
        string $ip
    ): void {

        $failedAttempts = LoginActivity::query()

            ->where(
                'ip_address',
                $ip
            )

            ->where(
                'status',
                'failed'
            )

            ->where(
                'created_at',
                '>=',
                now()->subMinutes(5)
            )

            ->count();

        if ($failedAttempts >= 5) {

            $exists = SuspiciousActivity::query()

                ->where(
                    'type',
                    'failed_logins'
                )

                ->where(
                    'ip_address',
                    $ip
                )

                ->where(
                    'created_at',
                    '>=',
                    now()->subMinutes(10)
                )

                ->exists();

            if (!$exists) {

                SuspiciousActivity::create([

                    'type' => 'failed_logins',

                    'severity' => 'critical',

                    'description' =>
                        'Multiple failed login attempts detected.',

                    'ip_address' => $ip,

                    'detected_at' => now(),

                    'meta' => [
                        'attempts' => $failedAttempts,
                    ],
                ]);
            }
        }
    }

    public function detectMultipleSessions(
        int $userId
    ): void {

        $activeSessions = LoginActivity::query()

            ->where(
                'user_id',
                $userId
            )

            ->whereNull(
                'logged_out_at'
            )

            ->count();

        if ($activeSessions >= 3) {

            $exists = SuspiciousActivity::query()

                ->where(
                    'type',
                    'multiple_sessions'
                )

                ->where(
                    'user_id',
                    $userId
                )

                ->where(
                    'created_at',
                    '>=',
                    now()->subMinutes(30)
                )

                ->exists();

            if (!$exists) {

                SuspiciousActivity::create([

                    'user_id' => $userId,

                    'type' => 'multiple_sessions',

                    'severity' => 'warning',

                    'description' =>
                        'Multiple simultaneous active sessions detected.',

                    'detected_at' => now(),

                    'meta' => [
                        'sessions' => $activeSessions,
                    ],
                ]);
            }
        }
    }
}
