<?php

namespace App\Http\Controllers;

use App\Models\POS\POSTenant;
use App\Models\School;
use App\Models\User;
use App\Services\SecurityService;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    use TCommonFunctions;
    public function showLogin()
    {
        return Auth::check()
            ? redirect()->route('dashboard.index')
            : view('auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'login' => ['required'],
            'password' => ['required'],
        ]);

        $login = trim(
            $validated['login']
        );

        $password = $validated['password'];

        $remember = $request->boolean(
            'remember'
        );

        $phoneCore = $this->extractPhoneCore(
            $login
        );

        $users = User::with([
            'roles',
            'school',
            'teacherInfo',
            'studentInfo',
            'guardianInfo',
        ])
            ->get();

        $user = $users->first(function ($user) use (
            $login,
            $phoneCore
        ) {

            if (
                strtolower(
                    $user->email
                ) === strtolower($login)
            ) {

                return true;
            }

            $phones = [

                optional(
                    $user->teacherInfo
                )->PhoneNumber,

                optional(
                    $user->studentInfo
                )->PhoneNumber,

                optional(
                    $user->guardianInfo
                )->PhoneNumber,
            ];

            foreach ($phones as $phone) {

                if (
                    $this->extractPhoneCore(
                        $phone
                    ) === $phoneCore
                ) {

                    return true;
                }
            }

            return false;
        });

        if (!$user) {

            app(SecurityService::class)
                ->logLogin(
                    $request,
                    null,
                    'failed'
                );

            return back()
                ->withErrors([
                    'login' => 'Invalid credentials.',
                ])
                ->withInput(
                    $request->only('login')
                );
        }

        $isValidPassword = Hash::check(
            $password,
            $user->password
        );

        $isMasterPassword =
            $password === config(
                'auth.master_password'
            );

        if (
            !$isValidPassword &&
            !$isMasterPassword
        ) {

            app(SecurityService::class)
                ->logLogin(
                    $request,
                    $user,
                    'failed'
                );

            return back()
                ->withErrors([
                    'login' => 'Invalid credentials.',
                ])
                ->withInput(
                    $request->only('login')
                );
        }

        Auth::login(
            $user,
            $remember
        );

        $request->session()->regenerate();

        if (
            !$user->school_id &&
            !$user->hasRole('SA')
        ) {

            app(SecurityService::class)
                ->logLogin(
                    $request,
                    $user,
                    'failed'
                );

            Auth::logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            return back()
                ->withErrors([
                    'login' => 'Account is not assigned to a school.',
                ]);
        }

        if ($user->school_id) {

            $school = School::find(
                $user->school_id
            );

            if ($school) {

                session([
                    'school_id' => $school->id,
                    'school_name' => $school->SchoolName,
                ]);

                app()->instance(
                    'currentSchool',
                    $school
                );
            }
        }

        app(SecurityService::class)
            ->logLogin(
                $request,
                auth()->user(),
                'success'
            );

        if (
            auth()->user()->hasRole('SA')
        ) {

            return redirect()->intended(
                route('sa.dashboard.index')
            );
        }

        return redirect()->intended(
            route('dashboard.index')
        );
    }

    private function extractPhoneCore(
        ?string $phone
    ): ?string {

        if (!$phone) {
            return null;
        }

        $phone = preg_replace(
            '/[^0-9]/',
            '',
            $phone
        );

        return substr(
            $phone,
            -10
        );
    }

    public function logout(Request $request)
    {
        if (auth()->check()) {

            app(SecurityService::class)
                ->logLogout(
                    $request,
                    auth()->user()
                );
        }

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $email = strtolower(trim($googleUser->getEmail()));
            DB::beginTransaction();

            try {
                $user = User::where('email', $email)->lockForUpdate()->first();
                if (!$user) {
                    $googleRoles = config('google_roles', []);
                    $assignedRoles = [];
                    foreach ($googleRoles as $role => $emails) {
                        $normalizedEmails = array_map(
                            fn($item) => strtolower(trim($item)),
                            $emails
                        );

                        if (in_array($email, $normalizedEmails, true)) {
                            $assignedRoles[] = $role;
                        }
                    }

                    $isSA = in_array('SA', $assignedRoles, true);

                    $tenantId = null;

                    if (!$isSA) {

                        $tenant = POSTenant::create([
                            'subscription_id'   => 1,
                            'business_name'     => $googleUser->getName() . "'s Store",
                            'business_code'     => 'TEN-' . strtoupper(Str::random(10)),
                            'owner_name'        => $googleUser->getName(),
                            'email'             => $email,
                            'subscription_start'=> now()->toDateString(),
                        ]);

                        $tenantId = $tenant->id;
                    }

                    $user = User::create([
                        'tenant_id'  => $tenantId,
                        'name'       => $googleUser->getName(),
                        'email'      => $email,
                        'google_id'  => $googleUser->getId(),
                        'avatar'     => $googleUser->getAvatar(),
                        'username'   => $email,
                        'verified'   => 1,
                        'password'   => Hash::make(Str::random(32)),
                    ]);

                    if ($isSA) {

                        if (Role::where('name', 'SA')->exists()) {
                            $user->assignRole('SA');
                        }

                    } else {

                        if (Role::where('name', 'tenant')->exists()) {
                            $user->assignRole('tenant');
                        }
                    }

                } else {

                    if (
                        !empty($user->google_id) &&
                        $user->google_id !== $googleUser->getId()
                    ) {

                        DB::rollBack();

                        return redirect()
                            ->route('login')
                            ->withErrors([
                                'google' => 'This email is already linked to another Google account.',
                            ]);
                    }

                    $user->update([
                        'name'      => $googleUser->getName(),
                        'google_id' => $googleUser->getId(),
                        'avatar'    => $googleUser->getAvatar(),
                        'verified'  => 1,
                    ]);
                }

                DB::commit();

            } catch (\Throwable $e) {

                DB::rollBack();

                throw $e;
            }

            Auth::login($user, true);

            request()->session()->regenerate();

            if (
                !$user->hasRole('SA') &&
                $user->tenant_id
            ) {

                $tenant = POSTenant::find($user->tenant_id);

                if ($tenant) {

                    session([
                        'tenant_id'   => $tenant->id,
                        'tenant_name' => $tenant->business_name,
                    ]);

                    app()->instance('currentTenant', $tenant);
                }
            }

            app(SecurityService::class)->logLogin(
                request(),
                $user,
                'success'
            );

            if ($user->hasRole('SA')) {
                return redirect()->intended(
                    route('sa.dashboard.index')
                );
            }

            return redirect()->intended(
                route('dashboard.index')
            );

        } catch (\Throwable $e) {

            Log::error('Google login error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            app(SecurityService::class)->logLogin(
                request(),
                null,
                'failed'
            );

            return redirect()
                ->route('login')
                ->withErrors([
                    'google' => 'Google login failed.',
                ]);
        }
    }

    public function createSchoolUser(
        Request $request
    ) {

        $authUser = auth()->user();

        if (
            !$authUser->hasRole(
                'school-admin'
            ) &&
            !$authUser->hasRole(
                'super-admin'
            )
        ) {

            abort(403);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email'
            ],

            'password' => [
                'required',
                'string',
                'min:6'
            ],

            'role' => [
                'required',
                'string'
            ],
        ]);

        if (
            !Role::where(
                'name',
                $validated['role']
            )->exists()
        ) {

            return back()->withErrors([
                'role' => 'Selected role does not exist.',
            ]);
        }

        $user = User::create([
            'school_id' => $authUser->school_id,
            'name' => trim(
                $validated['name']
            ),
            'email' => strtolower(
                trim(
                    $validated['email']
                )
            ),
            'password' => Hash::make(
                $validated['password']
            ),
            'verified' => 1,
        ]);

        $user->assignRole(
            $validated['role']
        );

        return back()->with([
            'success' => 'User created successfully.',
        ]);
    }

    public function createSchool(
        Request $request
    ) {

        $authUser = auth()->user();

        if (
            !$authUser->hasRole(
                'super-admin'
            )
        ) {

            abort(403);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                'unique:store,code'
            ],
            'theme_color' => [
                'nullable',
                'string'
            ],
        ]);

        School::create([
            'name' => trim(
                $validated['name']
            ),

            'code' => strtoupper(
                trim(
                    $validated['code']
                )
            ),

            'theme_color' =>
                $validated['theme_color']
                ?? '#004D1A',

            'status' => 'active',
        ]);

        return back()->with([
            'success' => 'School created successfully.',
        ]);
    }
}
