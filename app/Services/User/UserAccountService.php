<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserAccountService
{
    public function generateUsername(
        string $firstName,
        string $lastName,
        string $domain
    ): string {

        $base = preg_replace(
            '/[^a-z0-9]/',
            '',
            strtolower(
                substr(trim($firstName), 0, 1) .
                trim($lastName)
            )
        );

        $username = $base;

        $counter = 1;

        while (
        User::where(
            'email',
            $username . $domain
        )->exists()
        ) {

            $username = $base . $counter;

            $counter++;
        }

        return $username . $domain;
    }

    public function generatePassword(
        int $length = 8
    ): string {

        return strtoupper(
            Str::random($length)
        );
    }

    public function createUser(array $data): array
    {
        $plainPassword = $this->generatePassword();
        $user = User::create([
            'conn_id' => $data['conn_id'],
            'school_id' => $data['school_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($plainPassword),
            'qr_code' => $data['qr_code'],
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
            'status' => 'active',
            'archived' => 0,
        ]);

        if (!empty($data['role'])) {

            $user->assignRole($data['role']);
        }

        return [
            'user' => $user,
            'password' => $plainPassword
        ];
    }
}
