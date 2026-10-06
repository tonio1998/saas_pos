<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSProducts;
use App\Models\POS\POSSubscription;
use App\Models\POS\POSSubscriptionInvoice;
use App\Models\POS\POSTenant;
use App\Models\POS\POSTerminal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TenantBillingController extends Controller
{
    /**
     * Display Tenant Subscription & Billing History Hub
     */
    public function index(Request $request)
    {
        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id;
        $tenant = POSTenant::with(['subscription', 'pendingPlan', 'invoices' => function ($q) {
            $q->orderBy('id', 'desc');
        }])->findOrFail($tenantId);

        // Hardware & Catalog usage
        $currentTerminalsCount = POSTerminal::where('tenant_id', $tenantId)->count();
        $currentProductsCount = POSProducts::where('tenant_id', $tenantId)->count();

        // Calculate subscription days & status
        $now = Carbon::now();
        $subEnd = $tenant->subscription_end ? Carbon::parse($tenant->subscription_end) : null;
        $subStart = $tenant->subscription_start ? Carbon::parse($tenant->subscription_start) : null;

        $daysRemaining = null;
        $isOverdue = false;
        $isExpiringSoon = false;
        $progressPercent = 100;

        if ($subEnd) {
            $daysRemaining = (int) $now->diffInDays($subEnd, false);
            if ($daysRemaining < 0) {
                $isOverdue = true;
                $progressPercent = 100;
            } elseif ($subStart) {
                $totalCycleDays = max(1, $subStart->diffInDays($subEnd));
                $elapsedDays = $subStart->diffInDays($now);
                $progressPercent = min(100, max(0, round(($elapsedDays / $totalCycleDays) * 100)));
                $isExpiringSoon = $daysRemaining <= 7;
            }
        }

        // Active plans available for upgrade / renewal
        $availablePlans = POSSubscription::where('status', 'active')->orderBy('sort_order')->get();

        // Pending invoice if any
        $pendingInvoice = $tenant->invoices->where('payment_status', 'pending')->first();

        // Invoices pagination or list
        $invoices = $tenant->invoices;

        return view('pages.subscription.billing', compact(
            'tenant',
            'currentTerminalsCount',
            'currentProductsCount',
            'daysRemaining',
            'isOverdue',
            'isExpiringSoon',
            'progressPercent',
            'availablePlans',
            'pendingInvoice',
            'invoices'
        ));
    }

    /**
     * Show / Print official invoice statement for the tenant
     */
    public function showInvoice($id)
    {
        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id;
        $isSA = auth()->check() && (auth()->user()->hasRole('SA') || auth()->user()->is_super_admin);

        $invoice = POSSubscriptionInvoice::with(['tenant.mainBranch', 'subscription', 'verifier'])->findOrFail($id);

        if (!$isSA && (int)$invoice->tenant_id !== (int)$tenantId) {
            abort(403, 'Unauthorized access to this billing invoice.');
        }

        return view('pages.subscription.invoice_show', compact('invoice'));
    }

    /**
     * Cancel pending verification invoice so tenant can re-choose
     */
    public function cancelPending($id)
    {
        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id;
        $invoice = POSSubscriptionInvoice::where('tenant_id', $tenantId)->findOrFail($id);

        if ($invoice->payment_status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending invoices can be cancelled.');
        }

        $invoice->update([
            'payment_status' => 'cancelled',
            'notes' => ($invoice->notes ?? '') . ' | Cancelled by store owner on ' . now()->format('M d, Y h:i A'),
        ]);

        $tenant = POSTenant::find($tenantId);
        if ($tenant && $tenant->payment_status === 'pending_verification') {
            $tenant->update([
                'payment_status' => 'pending',
                'pending_plan_id' => null,
            ]);
        }

        return redirect()->route('subscription.billing')->with('success', 'Pending payment submission has been cancelled. You may now pick a new plan.');
    }
}
