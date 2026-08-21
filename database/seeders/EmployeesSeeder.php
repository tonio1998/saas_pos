<?php

namespace Database\Seeders;

use App\Models\Employees;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeesSeeder extends Seeder
{
    public function run(): void
    {
        DB::disableQueryLog();

        DB::transaction(function () {

            for ($i = 1; $i <= 100; $i++) {

                $gender = fake()->randomElement([
                    'male',
                    'female',
                ]);

                $firstName = $gender === 'male'
                    ? fake()->firstNameMale()
                    : fake()->firstNameFemale();

                $employee = Employees::create([
                    'UserID'      => 0,
                    'PhoneNumber' => '+639' . fake()->numerify('#########'),
                    'FirstName'   => $firstName,
                    'MiddleName'  => fake()->lastName(),
                    'LastName'    => fake()->lastName(),
                    'Suffix'      => fake()->randomElement([
                        null,
                        null,
                        'Jr.',
                        'Sr.',
                        'III',
                    ]),
                    'Address'     => fake()->address(),
                    'created_by'  => 1,
                    'updated_by'  => 1,
                    'created_at'  => now()->subDays(rand(1, 365)),
                    'updated_at'  => now(),
                    'status'      => 'active',
                    'archived'    => 0,
                ]);

                $base = strtolower(
                    preg_replace(
                        '/[^a-z0-9]/',
                        '',
                        substr($employee->FirstName, 0, 1) . $employee->LastName
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
                    'conn_id'   => $employee->id,
                    'SchoolID'  => env('SCHOOL_ID'),
                    'name'      => trim(
                        $employee->FirstName . ' ' . $employee->LastName
                    ),
                    'username'  => $username,
                    'email'     => $email,
                    'password'  => Hash::make('Password123'),
                    'qr_code'   => strtoupper(Str::random(15)),
                ]);

                $user->syncRoles(['employees']);

                $employee->UserID = $user->id;
                $employee->save();
            }
        });
    }
}
