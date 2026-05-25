<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
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

        if(auth()->user()->hasRole('SA')){
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
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {

            $googleUser = Socialite::driver('google')->user();

            $email = strtolower(
                trim($googleUser->getEmail())
            );

            $rolesConfig = config('google_roles', []);

            $assignedRole = null;

            foreach ($rolesConfig as $role => $emails) {

                $emails = array_map(
                    fn($item) => strtolower(trim($item)),
                    $emails
                );

                if (in_array($email, $emails)) {
                    $assignedRole = $role;
                    break;
                }
            }

            $user = User::where('email', $email)->first();

            /*
            |--------------------------------------------------------------------------
            | Auto Create User From Config
            |--------------------------------------------------------------------------
            */

            if (!$user && $assignedRole) {

                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $email,
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'verified' => 1,
                    'password' => bcrypt(\Illuminate\Support\Str::random(40)),
                ]);

                if (!$user->hasRole($assignedRole)) {
                    $user->assignRole($assignedRole);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Reject Unknown Users
            |--------------------------------------------------------------------------
            */

            if (!$user) {

                return redirect()
                    ->route('login')
                    ->withErrors([
                        'google' => 'Account not found.',
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Update Google Details
            |--------------------------------------------------------------------------
            */

            $user->update([
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'verified' => 1,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Ensure Config Role
            |--------------------------------------------------------------------------
            */

            if ($assignedRole) {

                if (!$user->hasRole($assignedRole)) {
                    $user->syncRoles([$assignedRole]);
                }
            }


            if (
                !$user->school_id &&
                !$user->hasRole('SA')
            ) {

                return redirect()
                    ->route('login')
                    ->withErrors([
                        'google' => 'Account is not assigned to a school.',
                    ]);
            }

            Auth::login($user, true);

            request()->session()->regenerate();

            app()->instance(
                'currentSchool',
                $user->school
            );

            if(auth()->user()->hasRole('SA')){
                return redirect()->intended(
                    route('sa.dashboard.index')
                );
            }

            return redirect()
                ->route('dashboard.index');


        } catch (\Throwable $e) {

            Log::error('Google login error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('login')
                ->withErrors([
                    'google' => 'Google login failed.',
                ]);
        }
    }

    public function createSchoolUser(Request $request)
    {
        $authUser = auth()->user();

        if (
            !$authUser->hasRole('school-admin') &&
            !$authUser->hasRole('super-admin')
        ) {

            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'string'],
        ]);

        if (!Role::where('name', $validated['role'])->exists()) {

            return back()->withErrors([
                'role' => 'Selected role does not exist.',
            ]);
        }

        $user = User::create([
            'school_id' => $authUser->school_id,
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'verified' => 1,
        ]);

        $user->assignRole($validated['role']);

        return back()->with([
            'success' => 'User created successfully.',
        ]);
    }

    public function createSchool(Request $request)
    {
        $authUser = auth()->user();

        if (!$authUser->hasRole('super-admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:schools,code'],
            'theme_color' => ['nullable', 'string'],
        ]);

        $school = School::create([
            'name' => trim($validated['name']),
            'code' => strtoupper(trim($validated['code'])),
            'theme_color' => $validated['theme_color'] ?? '#004D1A',
            'status' => 'active',
        ]);

        return back()->with([
            'success' => 'School created successfully.',
        ]);
    }
}
