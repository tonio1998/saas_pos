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
        $tenant->footer_text     = $validated['receipt_footer'] ?? null;
        $tenant->header_text     = $validated['header_text'] ?? null;

        $tenant->save();

        // Clear tenant subscription cache
        $this->subscriptionService->clearTenantCache($tenant->id);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Store profile, branding logo, and receipt settings updated successfully!',
                'logo_url' => $tenant->logo ? Storage::url($tenant->logo) : asset('images/no_image.jpg'),
            ]);
        }

        return redirect()
            ->route('settings.index')
            ->with('success', 'Store profile, branding logo, and receipt settings updated successfully!');
    }
}
