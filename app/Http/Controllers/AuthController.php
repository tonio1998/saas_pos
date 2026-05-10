<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

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
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if(Auth::attempt($credentials))
        {
            $request->session()->regenerate();
            return redirect('/');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials'
        ]);

    }

    public function register(Request $request)
    {
        try {

            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'min:6', 'confirmed'],
            ]);

            $email = strtolower($data['email']);

            $user = User::create([
                'name' => $data['name'],
                'email' => $email,
                'password' => bcrypt($data['password']),
            ]);

            $rolesConfig = config('google_roles');

            $assignedRole = 'guest';

            if (is_array($rolesConfig)) {
                foreach ($rolesConfig as $role => $emails) {
                    if (in_array($email, array_map('strtolower', $emails))) {
                        $assignedRole = $role;
                        break;
                    }
                }
            }

            if (!\Spatie\Permission\Models\Role::where('name', $assignedRole)->exists()) {
                throw new \Exception("Role '{$assignedRole}' does not exist.");
            }

            if (!$user->roles()->exists()) {
                $user->assignRole($assignedRole);
            }

            Auth::login($user);

            $request->session()->regenerate();

            return redirect()->route('dashboard.dashboard');

        } catch (\Illuminate\Validation\ValidationException $e) {

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();

        } catch (\Throwable $e) {

            \Log::error('Register error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withErrors('Registration failed. Please try again.')
                ->withInput();
        }
    }

    public function logout(Request $request)
    {

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');

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
                ['email' => $email],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt(uniqid())
                ]
            );

            $user->update([
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'verified' => 1,
            ]);

            $rolesConfig = config('google_roles');

            $assignedRole = 'guest';

            foreach ($rolesConfig as $role => $emails) {
                if (in_array($email, array_map('strtolower', $emails))) {
                    $assignedRole = $role;
                    break;
                }
            }

            if (!$user->roles()->exists()) {
                if (!\Spatie\Permission\Models\Role::where('name', $assignedRole)->exists()) {
                    throw new \Exception("Role '{$assignedRole}' does not exist.");
                }

                $user->assignRole($assignedRole);
            }

            Auth::login($user);

            return redirect()->route('dashboard');

        } catch (\Throwable $e) {
            report($e);
            dd($e);
            return redirect()->route('login')->withErrors('Google login failed.');
        }
    }

}
