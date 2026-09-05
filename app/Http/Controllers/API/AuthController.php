<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\POS\POSTenant;
use App\Models\User;
use App\Services\SecurityService;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    use TCommonFunctions;

    /**
     * Redirect to Google OAuth provider (Stateless for API / Mobile)
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Handle Google OAuth Callback (Stateless API & Webview callback)
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Throwable $e) {
            Log::error('API Google OAuth Callback Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hindi matagumpay ang Google Sign In. ' . $e->getMessage(),
            ], 400);
        }

        if (!$googleUser || !$googleUser->getEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Walang nakuhang email mula sa inyong Google Account.',
            ], 422);
        }

        $email = strtolower(trim($googleUser->getEmail()));
        $name  = trim($googleUser->getName() ?? $googleUser->getNickname() ?? 'Google User');
        $photo = $googleUser->getAvatar();

        return $this->processGoogleUser($request, $email, $name, $photo);
    }

    /**
     * Standard email/username/phone + password API login (Mirrors Web AuthController logic)
     */
    public function login(Request $request)
    {
        $login = trim($request->input('login', $request->input('email', $request->input('username', ''))));
        $password = $request->input('password', '');

        if (empty($login) || empty($password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email/Username and Password are required.',
            ], 422);
        }

        // Find user by email, username, or phone
        $user = User::where(function ($q) use ($login) {
            $q->where('email', $login)
              ->orWhere('username', $login);
        })->first();

        // Fallback: search phone number if applicable
        if (!$user && method_exists($this, 'extractPhoneCore')) {
            $phoneCore = $this->extractPhoneCore($login);
            if (!empty($phoneCore)) {
                $user = User::where('phone', 'LIKE', "%$phoneCore%")->first();
            }
        }

        if (!$user) {
            try {
                app(SecurityService::class)->logLogin($request, null, 'failed');
            } catch (\Throwable $e) {}

            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $isValidPassword = Hash::check($password, $user->password);
        $masterPassword = config('auth.master_password');
        $isMasterPassword = !empty($masterPassword) && ((string)$password === (string)$masterPassword);

        if (!$isValidPassword && !$isMasterPassword) {
            try {
                app(SecurityService::class)->logLogin($request, $user, 'failed');
            } catch (\Throwable $e) {}

            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        // Generate persistent API token (encoded userId + random string)
        $plainToken = base64_encode($user->id . '|' . Str::random(40));
        
        try {
            $user->update([
                'current_session_id' => Str::random(40),
            ]);
            app(SecurityService::class)->logLogin($request, $user, 'success');
        } catch (\Throwable $e) {}

        $tenant = $user->tenant_id ? POSTenant::find($user->tenant_id) : null;
        $roles = method_exists($user, 'getRoleNames') ? $user->getRoleNames() : ['cashier'];
        $permissions = method_exists($user, 'getAllPermissions') ? $user->getAllPermissions()->pluck('name') : [];

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token'   => $plainToken,
            'api_token' => $plainToken,
            'user' => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'username'  => $user->username,
                'avatar'    => $user->avatar ?? null,
                'role'      => $roles->first() ?? 'cashier',
                'tenant_id' => $user->tenant_id,
            ],
            'roles'       => $roles,
            'permissions' => $permissions,
            'tenant'      => $tenant ? [
                'id'              => $tenant->id,
                'business_name'   => $tenant->business_name,
                'business_code'   => $tenant->business_code,
                'owner_name'      => $tenant->owner_name,
                'email'           => $tenant->email,
                'phone'           => $tenant->phone,
                'address'         => $tenant->address,
                'logo'            => $tenant->logo,
                'tin'             => $tenant->tin,
                'branch_code'     => $tenant->branch_code,
                'bir_acc_no'      => $tenant->bir_acc_no,
                'bir_acc_date'    => $tenant->bir_acc_date,
                'bir_min'         => $tenant->bir_min,
                'bir_sn'          => $tenant->bir_sn,
                'header_text'     => $tenant->header_text,
                'footer_text'     => $tenant->footer_text,
                'currency_symbol' => $tenant->currency_symbol ?? '₱',
                'status'          => $tenant->status,
                'payment_status'  => $tenant->payment_status,
                'is_active'       => method_exists($tenant, 'isActive') ? $tenant->isActive() : true,
                'theme_settings'  => $tenant->theme_settings,
                'crm_settings'    => $tenant->crm_settings,
            ] : null,
        ]);
    }

    /**
     * Direct Google Sign-in API endpoint (Native Mobile SDK Token)
     */
    public function loginWithGoogle(Request $request)
    {
        $googleToken = $request->input('token'); // ID token from Google Sign-In SDK
        $email = $request->input('email');
        $name = $request->input('name');
        $photo = $request->input('photo');

        // Verify ID token with Google OAuth tokeninfo endpoint if token is provided
        if ($googleToken) {
            try {
                $http = Http::timeout(15);
                if (!app()->environment('production')) {
                    $http = $http->withoutVerifying();
                }
                $googleResponse = $http->get('https://oauth2.googleapis.com/tokeninfo', [
                    'id_token' => $googleToken,
                ]);

                if ($googleResponse->ok()) {
                    $googleUser = $googleResponse->json();
                    $email = $googleUser['email'] ?? $email;
                    $name = $googleUser['name'] ?? $name ?? $email;
                    $photo = $googleUser['picture'] ?? $photo;
                }
            } catch (\Throwable $e) {
                Log::warning('Google OAuth API verification fallback: ' . $e->getMessage());
            }
        }

        if (empty($email)) {
            return response()->json([
                'success' => false,
                'message' => 'Valid Google email is required.',
            ], 422);
        }

        $email = strtolower(trim($email));
        $name  = trim($name ?? explode('@', $email)[0]);

        return $this->processGoogleUser($request, $email, $name, $photo);
    }

    /**
     * Shared logic to find/create User + Tenant and generate response
     */
    protected function processGoogleUser(Request $request, string $email, string $name, ?string $photo = null)
    {
        DB::beginTransaction();
        try {
            $user = User::where('email', $email)->first();

            if (!$user) {
                // Auto create new store tenant (same as Web AuthController handleGoogleCallback)
                $tenant = POSTenant::create([
                    'subscription_id'    => 2, // Default to Suki Growth / Trial
                    'business_name'      => $name . "'s Store",
                    'business_code'      => 'MINI-' . strtoupper(Str::random(6)),
                    'owner_name'         => $name,
                    'email'              => $email,
                    'status'             => 'active',
                    'payment_status'     => 'pending',
                    'subscription_start' => now()->toDateString(),
                    'subscription_end'   => now()->addDays(30)->toDateString(),
                    'trial_ends_at'      => now()->addDays(7),
                ]);

                $user = User::create([
                    'tenant_id' => $tenant->id,
                    'name'      => $name,
                    'username'  => $email,
                    'email'     => $email,
                    'password'  => Hash::make(Str::random(16)),
                    'verified'  => 1,
                    'avatar'    => $photo,
                ]);

                try {
                    $role = Role::firstOrCreate(['name' => 'tenant', 'guard_name' => 'web']);
                    $user->assignRole($role);
                } catch (\Throwable $e) {}
            } else {
                if ($photo && empty($user->avatar)) {
                    $user->avatar = $photo;
                    $user->save();
                }
            }

            $plainToken = base64_encode($user->id . '|' . Str::random(40));
            try {
                $user->update([
                    'current_session_id' => Str::random(40),
                ]);
            } catch (\Throwable $e) {}

            try {
                app(SecurityService::class)->logLogin($request, $user, 'success');
            } catch (\Throwable $e) {}

            DB::commit();

            $tenant = $user->tenant_id ? POSTenant::find($user->tenant_id) : null;
            $roles = method_exists($user, 'getRoleNames') ? $user->getRoleNames() : ['tenant'];
            $permissions = method_exists($user, 'getAllPermissions') ? $user->getAllPermissions()->pluck('name') : [];

            return response()->json([
                'success' => true,
                'message' => 'Google authentication successful',
                'token'   => $plainToken,
                'api_token' => $plainToken,
                'user' => [
                    'id'        => $user->id,
                    'name'      => $user->name,
                    'email'     => $user->email,
                    'username'  => $user->username,
                    'avatar'    => $user->avatar ?? $photo,
                    'role'      => $roles->first() ?? 'tenant',
                    'tenant_id' => $user->tenant_id,
                ],
                'roles'       => $roles,
                'permissions' => $permissions,
                'tenant'      => $tenant ? [
                    'id'              => $tenant->id,
                    'business_name'   => $tenant->business_name,
                    'business_code'   => $tenant->business_code,
                    'owner_name'      => $tenant->owner_name,
                    'email'           => $tenant->email,
                    'phone'           => $tenant->phone,
                    'address'         => $tenant->address,
                    'logo'            => $tenant->logo,
                    'tin'             => $tenant->tin,
                    'branch_code'     => $tenant->branch_code,
                    'bir_acc_no'      => $tenant->bir_acc_no,
                    'bir_acc_date'    => $tenant->bir_acc_date,
                    'bir_min'         => $tenant->bir_min,
                    'bir_sn'          => $tenant->bir_sn,
                    'header_text'     => $tenant->header_text,
                    'footer_text'     => $tenant->footer_text,
                    'currency_symbol' => $tenant->currency_symbol ?? '₱',
                    'status'          => $tenant->status,
                    'payment_status'  => $tenant->payment_status,
                    'is_active'       => method_exists($tenant, 'isActive') ? $tenant->isActive() : true,
                    'theme_settings'  => $tenant->theme_settings,
                    'crm_settings'    => $tenant->crm_settings,
                ] : null,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('API Google Login Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Google Login failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get authenticated user profile /me
     */
    public function me(Request $request)
    {
        $token = $request->bearerToken() ?: $request->input('token');
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $userId = null;
        try {
            $decoded = base64_decode($token);
            if (str_contains($decoded, '|')) {
                $userId = explode('|', $decoded)[0];
            }
        } catch (\Throwable $e) {}

        $user = $userId ? User::find($userId) : User::where('current_session_id', $token)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired token.'], 401);
        }

        $tenant = $user->tenant_id ? POSTenant::find($user->tenant_id) : null;

        return response()->json([
            'success' => true,
            'user' => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'username'  => $user->username,
                'avatar'    => $user->avatar ?? null,
                'role'      => method_exists($user, 'getRoleNames') ? $user->getRoleNames()->first() : 'cashier',
                'tenant_id' => $user->tenant_id,
            ],
            'tenant' => $tenant ? [
                'id'              => $tenant->id,
                'business_name'   => $tenant->business_name,
                'business_code'   => $tenant->business_code,
                'owner_name'      => $tenant->owner_name,
                'email'           => $tenant->email,
                'phone'           => $tenant->phone,
                'address'         => $tenant->address,
                'logo'            => $tenant->logo,
                'tin'             => $tenant->tin,
                'branch_code'     => $tenant->branch_code,
                'bir_acc_no'      => $tenant->bir_acc_no,
                'bir_acc_date'    => $tenant->bir_acc_date,
                'bir_min'         => $tenant->bir_min,
                'bir_sn'          => $tenant->bir_sn,
                'header_text'     => $tenant->header_text,
                'footer_text'     => $tenant->footer_text,
                'currency_symbol' => $tenant->currency_symbol ?? '₱',
                'status'          => $tenant->status,
                'payment_status'  => $tenant->payment_status,
                'is_active'       => method_exists($tenant, 'isActive') ? $tenant->isActive() : true,
            ] : null,
        ]);
    }
}
