<?php

namespace Database\Seeders;

use App\Models\Parents;
use App\Models\Students;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentsSeeder extends Seeder
{
    public function run(): void
    {
        DB::disableQueryLog();

        DB::transaction(function () {

            $yearLevels = [
                7, 8, 9, 10, 11, 12
            ];

            $strands = [
                'STEM',
                'HUMSS',
                'ABM',
                'GAS',
                'TVL',
                'ICT',
            ];

            $guardianIds = Parents::pluck('id')->toArray();

            for ($i = 1; $i <= 100; $i++) {

                $gender = fake()->randomElement([
                    'male',
                    'female',
                ]);

                $firstName = $gender === 'male'
                    ? fake()->firstNameMale()
                    : fake()->firstNameFemale();

                $student = Students::create([
                    'UserID'      => 0,
                    'FirstName'   => $firstName,
                    'MiddleName'  => fake()->lastName(),
                    'LastName'    => fake()->lastName(),
                    'Suffix'      => fake()->randomElement([
                        null,
                        null,
                        'Jr.',
                        'III',
                    ]),
                    'LRN' => str_pad(
                        (string) fake()->unique()->numberBetween(
                            1,
                            999999999999
                        ),
                        12,
                        '0',
                        STR_PAD_LEFT
                    ),
                    'GuardianID'  => !empty($guardianIds)
                        ? fake()->randomElement($guardianIds)
                        : null,
                    'YearLevel'   => fake()->randomElement($yearLevels),
                    'Strand' => in_array($yearLevels, [11, 12])
                        ? fake()->randomElement($strands)
                        : null,
                    'PhoneNumber' => '+639' . fake()->numerify('#########'),
                    'filepath'    => null,
                    'created_by'  => 1,
                    'updated_by'  => 1,
                    'status'      => 'active',
                    'archived'    => 0,
                    'created_at'  => now()->subDays(rand(1, 365)),
                    'updated_at'  => now(),
                ]);

                $base = strtolower(
                    preg_replace(
                        '/[^a-z0-9]/',
                        '',
                        substr($student->FirstName, 0, 1) . $student->LastName
                    )
                );

                $username = $base;
                $counter = 1;

                while (
                User::where(
                    'email',
                    $username . env('SCHOOL_EMAIL')
                )->exists()
                ) {
                    $username = $base . $counter;
                    $counter++;
                }

                $email = $username . env('SCHOOL_EMAIL');

                $user = User::create([
                    'conn_id'   => $student->id,
                    'SchoolID'  => env('SCHOOL_ID'),
                    'name'      => trim(
                        $student->FirstName . ' ' . $student->LastName
                    ),
                    'username'  => $username,
                    'email'     => $email,
                    'password'  => Hash::make('Password123'),
                    'qr_code'   => strtoupper(Str::random(15)),
                ]);

                $user->syncRoles(['students']);

                $student->UserID = $user->id;
                $student->save();
            }
        });
    }
}
