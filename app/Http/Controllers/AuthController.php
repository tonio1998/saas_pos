<?php

namespace App\Http\Controllers;

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
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials['email'] = strtolower($credentials['email']);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors([
                    'email' => 'Invalid email or password.',
                ])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard.dashboard'));
    }

    public function register(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
            ]);

            $email = strtolower(trim($data['email']));

            $user = User::create([
                'name' => trim($data['name']),
                'email' => $email,
                'password' => Hash::make($data['password']),
            ]);

            $assignedRole = $this->resolveUserRole($email);

            if (!Role::where('name', $assignedRole)->exists()) {
                throw new \Exception("Role '{$assignedRole}' does not exist.");
            }

            if (!$user->roles()->exists()) {
                $user->assignRole($assignedRole);
            }

            Auth::login($user);

            $request->session()->regenerate();

            return redirect()->route('dashboard.dashboard');
        } catch (\Illuminate\Validation\ValidationException $e) {

            throw $e;

        } catch (\Throwable $e) {

            Log::error('Register error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withErrors([
                    'general' => 'Registration failed. Please try again.',
                ])
                ->withInput();
        }
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

            $email = strtolower($googleUser->getEmail());

            $user = User::firstOrCreate(
                [
                    'email' => $email,
                ],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make($email),
                    'verified' => 1,
                ]
            );

            $user->update([
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'verified' => 1,
            ]);

            $assignedRole = $this->resolveUserRole($email);

            if (!$user->roles()->exists()) {

                if (!Role::where('name', $assignedRole)->exists()) {
                    throw new \Exception("Role '{$assignedRole}' does not exist.");
                }

                $user->assignRole($assignedRole);
            }

            Auth::login($user);

            request()->session()->regenerate();

            return redirect()->route('dashboard');

        } catch (\Throwable $e) {

            Log::error('Google login error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('login')
                ->withErrors([
                    'google' => 'Google login failed. Please try again.',
                ]);
        }
    }

    private function resolveUserRole(string $email): string
    {
        $rolesConfig = config('google_roles', []);

        $email = strtolower($email);

        foreach ($rolesConfig as $role => $emails) {

            $emails = array_map('strtolower', (array) $emails);

            if (in_array($email, $emails, true)) {
                return $role;
            }
        }

        return 'guest';
    }
}
