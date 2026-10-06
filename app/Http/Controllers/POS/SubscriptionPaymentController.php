<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSTenant;
use App\Models\POS\POSSubscription;
use App\Models\POS\POSSubscriptionInvoice;
use App\Services\Tenant\TenantSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubscriptionPaymentController extends Controller
{
    /**
     * Show subscription checkout or verification pending status
     */
    public function checkout(Request $request)
    {
        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id;
        $tenant = POSTenant::with(['subscription', 'pendingPlan'])->find($tenantId);

        $plans = POSSubscription::where('status', 'active')
            ->orderBy('sort_order')
            ->get();

        // Flag if tenant wants to edit or re-submit their pending verification
        $isEditing = $request->has('edit') || $request->has('resubmit');

        return view('pages.subscription.checkout', compact('tenant', 'plans', 'isEditing'));
    }

    /**
     * Process manual payment submission (QRPH, GCash, Maya)
     * Sets payment_status to 'pending_verification' awaiting SuperAdmin approval.
     */
    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'plan_id'        => ['required', 'exists:pos_subscriptions,id'],
            'payment_method' => ['required', 'string', 'in:qrph,gcash,maya'],
            'reference_no'   => ['required', 'string', 'min:4', 'max:100'],
            'sender_name'    => ['required', 'string', 'max:255'],
            'sender_phone'   => ['nullable', 'string', 'max:50'],
            'proof_image'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id;
        $tenant = POSTenant::findOrFail($tenantId);
        $plan = POSSubscription::findOrFail($validated['plan_id']);

        $proofPath = $tenant->payment_proof;
        if ($request->hasFile('proof_image')) {
            // Delete previous proof if existing
            if ($tenant->payment_proof && Storage::disk('public')->exists($tenant->payment_proof)) {
                Storage::disk('public')->delete($tenant->payment_proof);
            }
            $proofPath = $request->file('proof_image')->store('payments/proofs', 'public');
        }

        $referenceNo = trim($validated['reference_no']);

        DB::transaction(function () use ($tenant, $plan, $validated, $referenceNo, $proofPath) {
            $tenant->update([
                'pending_plan_id'      => $plan->id,
                'payment_method'       => $validated['payment_method'],
                'payment_reference'    => $referenceNo,
                'payment_sender_name'  => trim($validated['sender_name']),
                'payment_sender_phone' => trim($validated['sender_phone'] ?? ''),
                'payment_amount'       => (float) $plan->effectivePrice(),
                'payment_proof'        => $proofPath,
                'payment_submitted_at' => now(),
                'payment_status'       => 'pending_verification',
                'payment_notes'        => $validated['notes'] ?? null,
            ]);

            // Create or update pending subscription invoice record
            POSSubscriptionInvoice::create([
                'tenant_id'            => $tenant->id,
                'subscription_id'      => $plan->id,
                'plan_name'            => $plan->name,
                'billing_cycle'        => $plan->billing_cycle ?? 'monthly',
                'duration_days'        => $plan->duration_days ?? 30,
                'max_terminals'        => $plan->max_terminals ?? 1,
                'max_products'         => $plan->max_products ?? 1000,
                'amount'               => (float) $plan->price,
                'discount_amount'      => ($plan->is_promo && $plan->promo_price) ? max(0, (float)$plan->price - (float)$plan->promo_price) : 0,
                'net_amount'           => (float) $plan->effectivePrice(),
                'payment_method'       => $validated['payment_method'],
                'payment_reference'    => $referenceNo,
                'payment_proof'        => $proofPath,
                'payment_sender_name'  => trim($validated['sender_name']),
                'payment_sender_phone' => trim($validated['sender_phone'] ?? ''),
                'payment_status'       => 'pending',
                'billing_date'         => now()->toDateString(),
                'due_date'             => now()->addDays(3)->toDateString(),
                'notes'                => $validated['notes'] ?? null,
            ]);
        });

        // Clear tenant subscription cache
        app(TenantSubscriptionService::class)->clearTenantCache($tenantId);

        $successMsg = "Salamat! Ang inyong pagbabayad (Ref # {$referenceNo}) para sa {$plan->name} ay naisumite na. Kasalukuyang bine-verify ito ng SuperAdmin. Maaari kayong tumawag o mag-text sa 0912-894-1731 para sa mabilisang activation.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'      => true,
                'message'      => $successMsg,
                'redirect_url' => route('subscription.checkout'),
            ]);
        }

        return redirect()->route('subscription.checkout')->with('success', $successMsg);
    }

    /**
     * Check current verification / subscription status
     */
    public function checkStatus()
    {
        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id;
        $tenant = POSTenant::with('subscription')->find($tenantId);

        return response()->json([
            'status'                  => $tenant?->status ?? 'unknown',
            'payment_status'          => $tenant?->payment_status ?? 'pending',
            'is_paid'                 => $tenant?->isPaid() ?? false,
            'is_pending_verification' => $tenant?->isPendingVerification() ?? false,
            'plan_name'               => $tenant?->subscription?->name ?? 'None',
            'expires_at'              => $tenant?->subscription_end ? $tenant->subscription_end->format('M d, Y') : null,
        ]);
    }

    /**
     * SuperAdmin: List all pending and historical payment verifications
     */
    public function saVerifications(Request $request)
    {
        abort_unless(auth()->check() && (auth()->user()->hasRole('SA') || auth()->user()->hasRole('admin') || auth()->user()->is_super_admin), 403);

        $statusFilter = $request->get('tab', 'pending');

        $query = POSTenant::with(['subscription', 'pendingPlan'])
            ->whereNotNull('payment_status');

        if ($statusFilter === 'pending') {
            $query->where('payment_status', 'pending_verification');
        } elseif ($statusFilter === 'paid') {
            $query->where('payment_status', 'paid');
        } elseif ($statusFilter === 'rejected') {
            $query->where('payment_status', 'rejected');
        }

        $tenants = $query->orderByDesc('payment_submitted_at')
            ->orderByDesc('updated_at')
            ->paginate(15);

        $counts = [
            'pending'  => POSTenant::where('payment_status', 'pending_verification')->count(),
            'paid'     => POSTenant::where('payment_status', 'paid')->count(),
            'rejected' => POSTenant::where('payment_status', 'rejected')->count(),
            'all'      => POSTenant::count(),
        ];

        return view('pages.sa.subscriptions.verifications', compact('tenants', 'counts', 'statusFilter'));
    }

    /**
     * SuperAdmin: Approve and activate subscription
     */
    public function saApproveVerification(Request $request, $id)
    {
        abort_unless(auth()->check() && (auth()->user()->hasRole('SA') || auth()->user()->hasRole('admin') || auth()->user()->is_super_admin), 403);

        $tenant = POSTenant::findOrFail($id);

        $targetPlanId = $tenant->pending_plan_id ?: $tenant->subscription_id ?: 2;
        $plan = POSSubscription::findOrFail($targetPlanId);
        $durationDays = (int) ($plan->duration_days ?? 30);

        // If current subscription is still active and in the future, extend from that end date
        $currentEnd = $tenant->subscription_end ? \Carbon\Carbon::parse($tenant->subscription_end) : null;
        if ($currentEnd && $currentEnd->isFuture()) {
            $startDate = $tenant->subscription_start ? \Carbon\Carbon::parse($tenant->subscription_start) : now();
            $endDate = $currentEnd->copy()->addDays($durationDays);
        } else {
            $startDate = now();
            $endDate = now()->addDays($durationDays);
        }

        $verifierName = auth()->user()->name ?? 'SuperAdmin';

        DB::transaction(function () use ($tenant, $plan, $startDate, $endDate, $verifierName, $durationDays) {
            $tenant->update([
                'subscription_id'    => $plan->id,
                'pending_plan_id'    => null,
                'payment_status'     => 'paid',
                'status'             => 'active',
                'paid_at'            => now(),
                'subscription_start' => $startDate->toDateString(),
                'subscription_end'   => $endDate->toDateString(),
                'payment_notes'      => "Verified and approved by {$verifierName} on " . now()->format('M d, Y h:i A'),
            ]);

            // Sync with invoice record
            $invoice = POSSubscriptionInvoice::where('tenant_id', $tenant->id)
                ->where('payment_status', 'pending')
                ->latest()
                ->first();

            if (!$invoice) {
                $invoice = new POSSubscriptionInvoice([
                    'tenant_id'            => $tenant->id,
                    'subscription_id'      => $plan->id,
                    'plan_name'            => $plan->name,
                    'billing_cycle'        => $plan->billing_cycle ?? 'monthly',
                    'duration_days'        => $durationDays,
                    'max_terminals'        => $plan->max_terminals ?? 1,
                    'max_products'         => $plan->max_products ?? 1000,
                    'amount'               => (float) $plan->effectivePrice(),
                    'discount_amount'      => 0,
                    'net_amount'           => (float) $plan->effectivePrice(),
                    'payment_method'       => $tenant->payment_method ?? 'manual_sa',
                    'payment_reference'    => $tenant->payment_reference ?? ('APPV-' . strtoupper(\Illuminate\Support\Str::random(6))),
                    'payment_sender_name'  => $tenant->payment_sender_name ?? $tenant->owner_name,
                    'payment_sender_phone' => $tenant->payment_sender_phone ?? $tenant->phone,
                    'billing_date'         => now()->toDateString(),
                    'due_date'             => now()->addDays(3)->toDateString(),
                ]);
            }

            $invoice->payment_status = 'paid';
            $invoice->paid_at = now();
            $invoice->period_start = $startDate->toDateString();
            $invoice->period_end = $endDate->toDateString();
            $invoice->verified_by = auth()->id();
            $invoice->notes = ($invoice->notes ? $invoice->notes . ' | ' : '') . "Verified by {$verifierName} on " . now()->format('M d, Y h:i A');
            $invoice->save();
        });

        // Clear tenant subscription cache
        app(TenantSubscriptionService::class)->clearTenantCache($tenant->id);

        return redirect()->back()->with('success', "Na-aprubahan at aktibo na ang subscription ng {$tenant->business_name} ({$plan->name}) hanggang {$endDate->format('M d, Y')}!");
    }

    /**
     * SuperAdmin: Reject payment with reason
     */
    public function saRejectVerification(Request $request, $id)
    {
        abort_unless(auth()->check() && (auth()->user()->hasRole('SA') || auth()->user()->hasRole('admin') || auth()->user()->is_super_admin), 403);

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $tenant = POSTenant::findOrFail($id);
        $verifierName = auth()->user()->name ?? 'SuperAdmin';

        DB::transaction(function () use ($tenant, $validated, $verifierName) {
            $tenant->update([
                'payment_status' => 'rejected',
                'payment_notes'  => $validated['rejection_reason'] . " (Reviewed by {$verifierName})",
            ]);

            $invoice = POSSubscriptionInvoice::where('tenant_id', $tenant->id)
                ->where('payment_status', 'pending')
                ->latest()
                ->first();

            if ($invoice) {
                $invoice->update([
                    'payment_status'   => 'rejected',
                    'rejection_reason' => $validated['rejection_reason'],
                    'verified_by'      => auth()->id(),
                    'notes'            => ($invoice->notes ? $invoice->notes . ' | ' : '') . "Rejected by {$verifierName}: " . $validated['rejection_reason'],
                ]);
            }
        });

        // Clear tenant subscription cache
        app(TenantSubscriptionService::class)->clearTenantCache($tenant->id);

        return redirect()->back()->with('warning', "Tinanggihan ang payment verification ng {$tenant->business_name}. Na-update ang reason.");
    }
}
