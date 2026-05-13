<?php

namespace Database\Seeders;

use App\Models\ScanLogs;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScanLogsSeeder extends Seeder
{
    public function run(): void
    {
        DB::disableQueryLog();

        DB::transaction(function () {
            $users = User::role([
                'students',
                'employees',
                'parents',
            ])->get();

            if ($users->isEmpty()) {
                return;
            }

            $gateNames = [
                'Main Gate',
                'North Gate',
                'South Gate',
                'East Gate',
                'West Gate',
            ];

            $remarks = [
                null,
                null,
                null,
                'Late arrival',
                'Manual verification',
                'Flagged by guard',
                'Duplicate scan detected',
            ];

            for ($i = 1; $i <= 50000; $i++) {
                $user = $users->random();
                $createdAt = fake()->dateTimeBetween(
                    '-30 days',
                    'now'
                );
                ScanLogs::create([
                    'VerificationCode' => strtoupper(
                        fake()->bothify('VC-#####')
                    ),
                    'UserID' => $user->id,
                    'Mode' => fake()->numberBetween(1, 3),
                    'lat' => fake()->latitude(
                        8.000000,
                        10.000000
                    ),
                    'lng' => fake()->longitude(
                        125.000000,
                        126.000000
                    ),
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => $createdAt,
                    'updated_at' => now(),
                    'status' => fake()->randomElement([
                        'active',
                        'inactive',
                        'locked',
                        'unlocked',
                    ]),
                    'archived' => 0,
                    'scan_type' => fake()->randomElement([
                        'qr',
                        'nfc',
                    ]),
                    'direction' => fake()->randomElement([
                        'entry',
                        'exit',
                    ]),
                    'attendance_status' => fake()->randomElement([
                        'present',
                        'late',
                        'flagged',
                    ]),
                    'gate_name' => fake()->randomElement(
                        $gateNames
                    ),
                    'remarks' => fake()->randomElement(
                        $remarks
                    ),
                ]);
            }
        });
    }
}
