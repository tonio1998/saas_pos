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
        $tenantId = auth()->user()->tenant_id;
        $tenant = POSTenant::where('id', $tenantId)->firstOrFail();

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
            'logo'            => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,svg', 'max:2048'],
            
            // Theme Customizer Fields
            'theme_preset'              => ['nullable', 'string', 'max:50'],
            'primary_color'             => ['nullable', 'string', 'max:20'],
            'topbar_color'              => ['nullable', 'string', 'max:20'],
            'topbar_text_color'         => ['nullable', 'string', 'max:20'],
            'sidebar_color'             => ['nullable', 'string', 'max:20'],
            'sidebar_text_color'        => ['nullable', 'string', 'max:20'],
            'sidebar_active_color'      => ['nullable', 'string', 'max:20'],
            'sidebar_active_text_color' => ['nullable', 'string', 'max:20'],
            'accent_color'              => ['nullable', 'string', 'max:20'],
            'dark_mode'                 => ['nullable', 'boolean'],
            
            // CRM & Loyalty Settings
            'points_per_peso'            => ['nullable', 'numeric', 'min:0'],
            'default_credit_limit'       => ['nullable', 'numeric', 'min:0'],
            'sms_receipt_enabled'        => ['nullable', 'boolean'],
            'sms_utang_reminder_enabled' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($tenant->logo && Storage::disk('public')->exists($tenant->logo)) {
                Storage::disk('public')->delete($tenant->logo);
            }

            $logoPath = $request->file('logo')->store('tenants/logos', 'public');
            $tenant->logo = $logoPath;
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

        // Theme Settings Payload with explicit text & background colors
        $tenant->theme_settings = [
            'preset'                    => $validated['theme_preset'] ?? $request->input('theme_preset', 'emerald'),
            'primary_color'             => $validated['primary_color'] ?? $request->input('primary_color', '#059669'),
            'topbar_color'              => $validated['topbar_color'] ?? $request->input('topbar_color', '#064E3B'),
            'topbar_text_color'         => $validated['topbar_text_color'] ?? $request->input('topbar_text_color', '#FFFFFF'),
            'sidebar_color'             => $validated['sidebar_color'] ?? $request->input('sidebar_color', '#0F172A'),
            'sidebar_text_color'        => $validated['sidebar_text_color'] ?? $request->input('sidebar_text_color', '#CBD5E1'),
            'sidebar_active_color'      => $validated['sidebar_active_color'] ?? $request->input('sidebar_active_color', '#059669'),
            'sidebar_active_text_color' => $validated['sidebar_active_text_color'] ?? $request->input('sidebar_active_text_color', '#FFFFFF'),
            'accent_color'              => $validated['accent_color'] ?? $request->input('accent_color', '#10B981'),
            'dark_mode'                 => (bool) ($validated['dark_mode'] ?? $request->input('dark_mode', false)),
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

        // Clear tenant subscription cache
        $this->subscriptionService->clearTenantCache($tenant->id);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Store profile, POS branding, system theme, and CRM settings saved successfully!',
                'logo_url' => $tenant->logo ? Storage::url($tenant->logo) : asset('images/no_image.jpg'),
                'theme' => $tenant->theme_settings,
                'crm' => $tenant->crm_settings,
            ]);
        }

        return redirect()
            ->route('settings.index')
            ->with('success', 'Store profile, branding logo, and receipt settings updated successfully!');
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

