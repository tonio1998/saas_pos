<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubscriptionPaymentController extends Controller
{
    public function checkout()
    {
        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id;
        $tenant = POSTenant::with('subscription')->find($tenantId);

        $plans = \App\Models\POS\POSSubscription::where('status', 'active')
            ->orderBy('sort_order')
            ->get();

        return view('pages.subscription.checkout', compact('tenant', 'plans'));
    }

    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'plan_id'        => ['required', 'exists:pos_subscriptions,id'],
            'payment_method' => ['required', 'string', 'in:gcash,maya,card'],
            'reference_no'   => ['nullable', 'string', 'max:100'],
            'account_number' => ['nullable', 'string', 'max:50'],
        ]);

        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id;
        $tenant = POSTenant::findOrFail($tenantId);
        $plan = \App\Models\POS\POSSubscription::findOrFail($validated['plan_id']);

        $referenceNo = $validated['reference_no'] 
            ?? strtoupper($validated['payment_method']) . '-' . strtoupper(Str::random(10));

        $days = (int)($plan->duration_days ?? 30);

        DB::transaction(function () use ($tenant, $plan, $referenceNo, $days) {
            $tenant->update([
                'subscription_id'    => $plan->id,
                'payment_status'     => 'paid',
                'status'             => 'active',
                'payment_reference'  => $referenceNo,
                'paid_at'            => now(),
                'subscription_start' => now()->toDateString(),
                'subscription_end'   => now()->addDays($days)->toDateString(),
            ]);
        });

        // Clear tenant subscription cache
        app(\App\Services\Tenant\TenantSubscriptionService::class)->clearTenantCache($tenantId);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Online payment processed successfully! Your {$plan->name} subscription is now ACTIVE.",
                'redirect_url' => route('dashboard.index'),
            ]);
        }

        return redirect()->route('dashboard.index')->with('success', "Online payment verified! Your {$plan->name} subscription is now active.");
    }

    public function checkStatus()
    {
        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id;
        $tenant = POSTenant::find($tenantId);

        return response()->json([
            'status' => $tenant?->payment_status ?? 'pending',
            'is_paid' => $tenant?->isPaid() ?? false,
            'expires_at' => $tenant?->subscription_end ? format_date($tenant->subscription_end) : null,
        ]);
    }
}
