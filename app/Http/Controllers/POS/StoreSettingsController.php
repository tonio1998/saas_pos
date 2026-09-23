<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSTenant;
use App\Services\Tenant\TenantSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreSettingsController extends Controller
{
    protected TenantSubscriptionService $subscriptionService;

    public function __construct(TenantSubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Display Store Profile, Logo & Subscription Settings Page
     */
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        $tenant = POSTenant::with('subscription')->find($tenantId);

        if (!$tenant) {
            // Fallback for system admin or unassigned user
            $tenant = POSTenant::with('subscription')->first();
        }

        $usage = $tenant ? $this->subscriptionService->getUsageSummary($tenant->id) : [];

        // Days remaining in subscription / trial
        $daysRemaining = 0;
        if ($tenant && $tenant->subscription_end) {
            $daysRemaining = max(0, (int) now()->startOfDay()->diffInDays($tenant->subscription_end->startOfDay(), false));
        }

        return view('pages.tenants.settings.store', compact('tenant', 'usage', 'daysRemaining'));
    }

    /**
     * Update Store Profile, Branding Logo, Tax & Receipt Settings
     */
    public function update(Request $request)
    {
        $tenantId = auth()->user()?->tenant_id ?: session('tenant_id');
        $tenant = $tenantId ? POSTenant::find($tenantId) : null;
        if (!$tenant) {
            $tenant = POSTenant::first();
        }

        if (!$tenant) {
            return back()->with('error', 'Store tenant profile not found.');
        }

        $validated = $request->validate([
            'business_name'   => ['required', 'string', 'max:255'],
            'owner_name'      => ['required', 'string', 'max:255'],
            'phone'           => ['nullable', 'string', 'max:50'],
            'email'           => ['nullable', 'email', 'max:255'],
            'address'         => ['nullable', 'string', 'max:500'],
            'tin'             => ['nullable', 'string', 'max:50'],
            'receipt_footer'  => ['nullable', 'string', 'max:500'],
            'header_text'     => ['nullable', 'string', 'max:500'],
            'currency_symbol' => ['nullable', 'string', 'max:10'],
            'logo'               => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,svg,bmp', 'max:10240'],
            'logo_square'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,svg,bmp', 'max:10240'],
            'remove_logo'        => ['nullable', 'boolean'],
            'remove_logo_square' => ['nullable', 'boolean'],
            
            // Theme Customizer Fields
            'theme_preset'              => ['nullable', 'string', 'max:50'],
            'gradient'                  => ['nullable', 'string', 'max:255'],
            'primary_color'             => ['nullable', 'string', 'max:20'],
            'topbar_color'              => ['nullable', 'string', 'max:20'],
            'topbar_text_color'         => ['nullable', 'string', 'max:20'],
            'sidebar_color'             => ['nullable', 'string', 'max:20'],
            'sidebar_text_color'        => ['nullable', 'string', 'max:20'],
            'sidebar_active_color'      => ['nullable', 'string', 'max:20'],
            'sidebar_active_text_color' => ['nullable', 'string', 'max:20'],
            'accent_color'              => ['nullable', 'string', 'max:20'],
            'dark_mode'                 => ['nullable', 'boolean'],
            'logo_sidebar_height'       => ['nullable', 'integer', 'min:20', 'max:80'],
            'logo_topbar_height'        => ['nullable', 'integer', 'min:20', 'max:60'],
            'logo_receipt_height'       => ['nullable', 'integer', 'min:25', 'max:100'],
            
            // CRM & Loyalty Settings
            'points_per_peso'            => ['nullable', 'numeric', 'min:0'],
            'default_credit_limit'       => ['nullable', 'numeric', 'min:0'],
            'sms_receipt_enabled'        => ['nullable', 'boolean'],
            'sms_utang_reminder_enabled' => ['nullable', 'boolean'],
        ]);

        // 1. Horizontal / Primary Logo Upload or Removal
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            if ($file->isValid()) {
                if ($tenant->logo && Storage::disk('public')->exists($tenant->logo)) {
                    Storage::disk('public')->delete($tenant->logo);
                }
                $filename = 'logo_rect_' . $tenant->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $logoPath = $file->storeAs('tenants/logos', $filename, 'public');
                $tenant->logo = $logoPath;
            }
        } elseif ($request->boolean('remove_logo')) {
            if ($tenant->logo && Storage::disk('public')->exists($tenant->logo)) {
                Storage::disk('public')->delete($tenant->logo);
            }
            $tenant->logo = null;
        }

        // 2. Square Icon / Receipt Stamp Upload or Removal
        if ($request->hasFile('logo_square')) {
            $fileSquare = $request->file('logo_square');
            if ($fileSquare->isValid()) {
                if ($tenant->logo_square && Storage::disk('public')->exists($tenant->logo_square)) {
                    Storage::disk('public')->delete($tenant->logo_square);
                }
                $filenameSquare = 'logo_sq_' . $tenant->id . '_' . time() . '.' . $fileSquare->getClientOriginalExtension();
                $logoSquarePath = $fileSquare->storeAs('tenants/logos', $filenameSquare, 'public');
                $tenant->logo_square = $logoSquarePath;
            }
        } elseif ($request->boolean('remove_logo_square')) {
            if ($tenant->logo_square && Storage::disk('public')->exists($tenant->logo_square)) {
                Storage::disk('public')->delete($tenant->logo_square);
            }
            $tenant->logo_square = null;
        }

        $tenant->business_name   = trim($validated['business_name']);
        $tenant->owner_name      = trim($validated['owner_name']);
        $tenant->phone           = $validated['phone'] ?? null;
        $tenant->email           = $validated['email'] ?? null;
        $tenant->address         = $validated['address'] ?? null;
        $tenant->tin             = $validated['tin'] ?? null;
        $tenant->currency_symbol = $validated['currency_symbol'] ?? '₱';
        $tenant->footer_text     = $validated['receipt_footer'] ?? null;
        $tenant->header_text     = $validated['header_text'] ?? null;

        // Theme Settings Payload with explicit text & background colors and dynamic logo dimensions
        $tenant->theme_settings = [
            'preset'                    => $validated['theme_preset'] ?? $request->input('theme_preset', 'emerald'),
            'gradient'                  => $validated['gradient'] ?? $request->input('gradient', null),
            'primary_color'             => $validated['primary_color'] ?? $request->input('primary_color', '#059669'),
            'topbar_color'              => $validated['topbar_color'] ?? $request->input('topbar_color', '#064E3B'),
            'topbar_text_color'         => $validated['topbar_text_color'] ?? $request->input('topbar_text_color', '#FFFFFF'),
            'sidebar_color'             => $validated['sidebar_color'] ?? $request->input('sidebar_color', '#0F172A'),
            'sidebar_text_color'        => $validated['sidebar_text_color'] ?? $request->input('sidebar_text_color', '#CBD5E1'),
            'sidebar_active_color'      => $validated['sidebar_active_color'] ?? $request->input('sidebar_active_color', '#059669'),
            'sidebar_active_text_color' => $validated['sidebar_active_text_color'] ?? $request->input('sidebar_active_text_color', '#FFFFFF'),
            'accent_color'              => $validated['accent_color'] ?? $request->input('accent_color', '#10B981'),
            'dark_mode'                 => (bool) ($validated['dark_mode'] ?? $request->input('dark_mode', false)),
            'logo_sidebar_height'       => (int) ($validated['logo_sidebar_height'] ?? $request->input('logo_sidebar_height', 38)),
            'logo_topbar_height'        => (int) ($validated['logo_topbar_height'] ?? $request->input('logo_topbar_height', 32)),
            'logo_receipt_height'       => (int) ($validated['logo_receipt_height'] ?? $request->input('logo_receipt_height', 52)),
        ];

        // CRM Settings Payload
        $rawPoints = (float) $request->input('points_per_peso', 1);
        $pointsPerPeso = $rawPoints > 0.5 ? ($rawPoints / 100) : $rawPoints;

        $tenant->crm_settings = [
            'points_per_peso'            => $pointsPerPeso,
            'default_credit_limit'       => (float) $request->input('default_credit_limit', 5000),
            'sms_receipt_enabled'        => (bool) $request->input('sms_receipt_enabled', true),
            'sms_utang_reminder_enabled' => (bool) $request->input('sms_utang_reminder_enabled', true),
        ];

        $tenant->save();

        // Update session branding
        session([
            'tenant_id'   => $tenant->id,
            'tenant_name' => $tenant->business_name,
            'tenant_logo' => $tenant->logo,
        ]);

        // Clear tenant subscription cache
        $this->subscriptionService->clearTenantCache($tenant->id);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Store profile, POS branding logo, theme palette, and CRM settings saved successfully!',
                'logo_url' => $tenant->logo ? Storage::url($tenant->logo) : asset('images/no_image.jpg'),
                'theme' => $tenant->theme_settings,
                'crm' => $tenant->crm_settings,
            ]);
        }

        return redirect()
            ->route('settings.index')
            ->with('success', 'Store profile, branding logo, and POS theme settings saved successfully!');
    }

    /**
     * Live 78mm / 58mm Thermal Receipt Preview
     */
    public function previewReceipt(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $tenant = POSTenant::find($tenantId);

        if (!$tenant) {
            $tenant = POSTenant::first();
        }

        return view('pages.tenants.settings.receipt_preview', compact('tenant'));
    }
}

