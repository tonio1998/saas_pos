<?php

namespace App\Traits;

use Spatie\Permission\Models\Role;

trait HandlesGoogleRoles
{
    /**
     * Resolve pre-defined role(s) for a given email from config('google_roles')
     *
     * @param string $email
     * @return array<string>
     */
    public function getGoogleConfiguredRoles(string $email): array
    {
        $config = config('google_roles', []);
        $email = strtolower(trim($email));
        $roles = [];

        if (!is_array($config)) {
            return [];
        }

        foreach ($config as $key => $value) {
            if (is_array($value)) {
                // Format: 'SA' => ['email1', 'email2']
                $emails = array_map(fn($e) => strtolower(trim($e)), $value);
                if (in_array($email, $emails, true)) {
                    $roles[] = (string) $key;
                }
            } elseif (is_string($value)) {
                // Format: 'email1' => 'SA'
                if (strtolower(trim($key)) === $email) {
                    $roles[] = $value;
                }
            }
        }

        return array_values(array_unique($roles));
    }

    /**
     * Apply pre-defined Google roles to a user
     */
    public function syncPredefinedGoogleRoles($user, array $predefinedRoles): void
    {
        if (empty($predefinedRoles)) {
            return;
        }

        foreach ($predefinedRoles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            if (!$user->hasRole($roleName)) {
                $user->assignRole($role);
            }
        }

        if (in_array('SA', $predefinedRoles, true)) {
            $user->is_super_admin = 1;
            $user->save();
        }
    }
}
