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
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::error('Google Auth Error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['login' => 'Hindi matagumpay ang Google Sign In. Mangyaring subukan uli.']);
        }

        if (!$googleUser || !$googleUser->getEmail()) {
            return redirect()->route('login')->withErrors(['login' => 'Walang nakuhang email mula sa inyong Google Account.']);
        }

        $email = strtolower(trim($googleUser->getEmail()));
        $name  = trim($googleUser->getName() ?? $googleUser->getNickname() ?? 'Google User');

        DB::beginTransaction();
        try {
            $user = User::where('email', $email)->first();

            if (!$user) {
                // Auto create new store tenant
                $tenant = POSTenant::create([
                    'subscription_id'    => 2, // Default to Suki Growth
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
                ]);

                $role = Role::firstOrCreate(['name' => 'tenant', 'guard_name' => 'web']);
                $user->assignRole($role);
            }

            // Update session ID for single device protection
            $sessionId = session()->getId();
            $user->update(['current_session_id' => $sessionId]);

            Auth::login($user, true);

            $tenantId = $user->tenant_id;
            $tenantName = null;
            if ($tenantId) {
                $tenantObj = POSTenant::find($tenantId);
                $tenantName = $tenantObj ? $tenantObj->business_name : null;
            }

            session([
                'just_authenticated' => true,
                'tenant_id'   => $tenantId,
                'tenant_name' => $tenantName,
            ]);

            app(SecurityService::class)->logLogin($request, $user, 'success');
            DB::commit();

            return redirect()->route('dashboard.index')->with('success', 'Naka-log in na gamit ang Google Account!');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Google Login Process Error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['login' => 'Nagkaroon ng problema sa pagproseso ng inyong Google login.']);
        }
    }

    public function showLogin()
    {
        return Auth::check()
            ? redirect()->route('dashboard.index')
            : view('auth.login');
    }

    public function submitReview(Request $request)
    {
        $validated = $request->validate([
            'reviewer_name' => ['required', 'string', 'max:255'],
            'store_name'    => ['required', 'string', 'max:255'],
            'rating'        => ['required', 'integer', 'min:1', 'max:5'],
            'review_text'   => ['required', 'string', 'max:1000'],
        ]);

        $nameParts = explode(' ', trim($validated['reviewer_name']));
        $initials = '';
        foreach ($nameParts as $part) {
            if (!empty($part)) {
                $initials .= strtoupper(substr($part, 0, 1));
            }
        }
        $initials = substr($initials, 0, 2) ?: 'ST';

        \App\Models\POS\POSStoreReview::create([
            'tenant_id'       => auth()->check() ? auth()->user()->tenant_id : null,
            'reviewer_name'   => trim($validated['reviewer_name']),
            'store_name'      => trim($validated['store_name']),
            'avatar_initials' => $initials,
            'rating'          => (int) $validated['rating'],
            'review_text'     => trim($validated['review_text']),
            'is_approved'     => 1,
            'is_featured'     => 0,
        ]);

        return redirect()->back()->with('success', 'Salamat sa inyong feedback! Na-ipost na ang inyong review sa LikhaPOS.');
    }

    public function showRegister(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard.index');
        }

        $selectedPlanId = (int) $request->query('plan', 2);
        $subscriptions = \App\Models\POS\POSSubscription::where('status', 'active')
            ->orderBy('sort_order')
            ->get();

        return view('auth.register', compact('subscriptions', 'selectedPlanId'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'business_name'   => ['required', 'string', 'max:255'],
            'subscription_id' => ['required', 'exists:pos_subscriptions,id'],
            'email'           => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'        => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        DB::beginTransaction();
        try {
            $tenant = POSTenant::create([
                'subscription_id'    => $validated['subscription_id'],
                'business_name'      => trim($validated['business_name']),
                'business_code'      => 'MINI-' . strtoupper(Str::random(6)),
                'owner_name'         => trim($validated['name']),
                'email'              => strtolower(trim($validated['email'])),
                'phone'              => $validated['phone'] ?? null,
                'address'            => $validated['address'] ?? null,
                'tin'                => $validated['tin'] ?? null,
                'status'             => 'active',
                'payment_status'     => 'pending',
                'subscription_start' => now()->toDateString(),
                'subscription_end'   => now()->addDays(30)->toDateString(),
                'trial_ends_at'      => now()->addDays(7),
            ]);

            $user = User::create([
                'tenant_id' => $tenant->id,
                'name'      => trim($validated['name']),
                'username'  => strtolower(trim($validated['email'])),
                'email'     => strtolower(trim($validated['email'])),
                'password'  => Hash::make($validated['password']),
                'verified'  => 1,
            ]);

            $role = Role::firstOrCreate(['name' => 'tenant']);
            $user->assignRole($role);

            DB::commit();

            Auth::login($user);
            $request->session()->regenerate();

            session([
                'just_authenticated' => true,
                'tenant_id'   => $tenant->id,
                'tenant_name' => $tenant->business_name,
            ]);

            app(SecurityService::class)->logLogin($request, $user, 'success');

            return redirect()->route('subscription.checkout')->with('success', 'Registration successful! Welcome to RetailPOS. Please complete your online subscription payment to unlock full POS features.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Registration Error', ['error' => $e->getMessage()]);
            return back()->withErrors(['email' => 'Registration failed: ' . $e->getMessage()])->withInput();
        }
    }

    public function completeStoreProfile(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->tenant_id) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'phone'   => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:500'],
            'tin'     => ['nullable', 'string', 'max:50'],
        ]);

        $tenant = POSTenant::find($user->tenant_id);
        if ($tenant) {
            $tenant->update([
                'phone'   => trim($validated['phone']),
                'address' => trim($validated['address']),
                'tin'     => !empty($validated['tin']) ? trim($validated['tin']) : null,
            ]);
        }

        return redirect()->back()->with('success', 'Kumpleto na ang impormasyon ng iyong store!');
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
        session(['just_authenticated' => true]);

        $user->update([
            'current_session_id' => $request->session()->getId(),
        ]);

        if (
            !$user->tenant_id &&
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
                    'login' => 'Account is not assigned to a minimart store.',
                ]);
        }

        if ($user->tenant_id) {
            $tenant = POSTenant::find($user->tenant_id);
            if ($tenant) {
                session([
                    'tenant_id'   => $tenant->id,
                    'tenant_name' => $tenant->business_name,
                ]);
            }
        } elseif ($user->school_id) {

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
            auth()->user()->update([
                'current_session_id' => null,
            ]);

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

        $tenantId = session('tenant_id') ?? $authUser->tenant_id;
        if ($tenantId) {
            $check = app(\App\Services\Tenant\TenantSubscriptionService::class)->canCreateUser($tenantId, $validated['role']);
            if (!$check['allowed']) {
                return back()->withErrors(['role' => $check['message']])->withInput();
            }
        }

        $user = User::create([
            'school_id' => $authUser->school_id,
            'name' => trim(
                $validated['name']
            ),
            'username' => strtolower(
                trim(
                    $validated['email']
                )
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
