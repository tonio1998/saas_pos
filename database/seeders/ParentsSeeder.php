<?php

namespace Database\Seeders;

use App\Models\Parents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ParentsSeeder extends Seeder
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

                $parent = Parents::create([
                    'UserID'      => 0,
                    'PhoneNumber' => '09' . fake()->numerify('#########'),
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
                    'status'      => 'active',
                    'archived'    => 0,
                    'created_at'  => now()->subDays(rand(1, 365)),
                    'updated_at'  => now(),
                ]);

                $base = strtolower(
                    preg_replace(
                        '/[^a-z0-9]/',
                        '',
                        substr($parent->FirstName, 0, 1) . $parent->LastName
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
                    'conn_id'   => $parent->id,
                    'SchoolID'  => env('SCHOOL_ID'),
                    'name'      => trim(
                        $parent->FirstName . ' ' . $parent->LastName
                    ),
                    'email'     => $email,
                    'password'  => Hash::make('Password123'),
                    'qr_code'   => strtoupper(Str::random(15)),
                ]);

                $user->syncRoles(['parents']);

                $parent->UserID = $user->id;
                $parent->save();
            }
        });
    }
}
