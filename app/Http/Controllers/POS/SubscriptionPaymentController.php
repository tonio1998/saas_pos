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
        $tenant = POSTenant::find($tenantId);

        $plans = [
            [
                'id' => 'monthly',
                'name' => 'Minimart Monthly Plan',
                'price' => 999.00,
                'period' => 'Monthly',
                'description' => 'Complete POS & CRM features for 1 store, unlimited sales, inventory & BIR receipts.',
                'popular' => false,
            ],
            [
                'id' => 'annual',
                'name' => 'Minimart Annual Pro',
                'price' => 9999.00,
                'period' => 'Yearly (Save ₱1,989)',
                'description' => 'Full access with priority support, multi-device sync & automated BIR tax reporting.',
                'popular' => true,
            ]
        ];

        return view('pages.subscription.checkout', compact('tenant', 'plans'));
    }

    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'plan_id'        => ['required', 'string', 'in:monthly,annual'],
            'payment_method' => ['required', 'string', 'in:gcash,maya,card'],
            'reference_no'   => ['nullable', 'string', 'max:100'],
            'account_number' => ['nullable', 'string', 'max:50'],
        ]);

        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id;
        $tenant = POSTenant::findOrFail($tenantId);

        $referenceNo = $validated['reference_no'] 
            ?? strtoupper($validated['payment_method']) . '-' . strtoupper(Str::random(10));

        $days = $validated['plan_id'] === 'annual' ? 365 : 30;

        DB::transaction(function () use ($tenant, $referenceNo, $days) {
            $tenant->update([
                'payment_status'     => 'paid',
                'status'             => 'active',
                'payment_reference'  => $referenceNo,
                'paid_at'            => now(),
                'subscription_start' => now()->toDateString(),
                'subscription_end'   => now()->addDays($days)->toDateString(),
            ]);
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Online payment processed successfully! Your Minimart POS subscription is now ACTIVE.',
                'redirect_url' => route('dashboard.index'),
            ]);
        }

        return redirect()->route('dashboard.index')->with('success', 'Online payment verified! Your POS system is fully activated.');
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
