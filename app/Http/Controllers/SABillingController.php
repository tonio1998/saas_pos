<?php

namespace App\Http\Controllers;

use App\Models\POS\POSSubscription;
use App\Models\POS\POSSubscriptionInvoice;
use App\Models\POS\POSTenant;
use App\Services\Tenant\TenantSubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SABillingController extends Controller
{
    /**
     * Display Platform Billing, Invoices & Subscription History Ledger
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'all');
        $search = trim($request->query('search', ''));
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $query = POSSubscriptionInvoice::with(['tenant', 'subscription', 'verifier'])
            ->orderBy('id', 'desc');

        // Tab filters
        if ($tab === 'paid') {
            $query->where('payment_status', 'paid');
        } elseif ($tab === 'pending') {
            $query->where('payment_status', 'pending');
        } elseif ($tab === 'overdue') {
            $query->where(function ($q) {
                $q->where('payment_status', 'overdue')
                  ->orWhere(function ($sub) {
                      $sub->where('payment_status', 'pending')
                          ->where('due_date', '<', now()->toDateString());
                  });
            });
        } elseif ($tab === 'rejected') {
            $query->where('payment_status', 'rejected');
        }

        // Search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhere('payment_reference', 'like', "%{$search}%")
                  ->orWhere('payment_sender_name', 'like', "%{$search}%")
                  ->orWhere('plan_name', 'like', "%{$search}%")
                  ->orWhereHas('tenant', function ($t) use ($search) {
                      $t->where('business_name', 'like', "%{$search}%")
                        ->orWhere('owner_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Date range
        if (!empty($dateFrom)) {
            $query->whereDate('billing_date', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $query->whereDate('billing_date', '<=', $dateTo);
        }

        $invoices = $query->paginate(15)->withQueryString();

        // Financial KPIs
        $totalCollected = POSSubscriptionInvoice::where('payment_status', 'paid')->sum('net_amount');
        $thisMonthCollected = POSSubscriptionInvoice::where('payment_status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('net_amount');

        $pendingAmount = POSSubscriptionInvoice::where('payment_status', 'pending')->sum('net_amount');
        $pendingCount = POSSubscriptionInvoice::where('payment_status', 'pending')->count();

        $overdueCount = POSSubscriptionInvoice::where(function ($q) {
            $q->where('payment_status', 'overdue')
              ->orWhere(function ($sub) {
                  $sub->where('payment_status', 'pending')
                      ->where('due_date', '<', now()->toDateString());
              });
        })->count();

        $allCount = POSSubscriptionInvoice::count();
        $paidCount = POSSubscriptionInvoice::where('payment_status', 'paid')->count();
        $rejectedCount = POSSubscriptionInvoice::where('payment_status', 'rejected')->count();

        // Data for manual invoice creation modal
        $activePlans = POSSubscription::where('status', 'active')->orderBy('sort_order')->get();
        $tenantsList = POSTenant::orderBy('business_name')->get(['id', 'business_name', 'owner_name', 'subscription_id', 'subscription_end']);

        return view('pages.sa.subscriptions.billing', compact(
            'invoices',
            'tab',
            'search',
            'dateFrom',
            'dateTo',
            'totalCollected',
            'thisMonthCollected',
            'pendingAmount',
            'pendingCount',
            'overdueCount',
            'allCount',
            'paidCount',
            'rejectedCount',
            'activePlans',
            'tenantsList'
        ));
    }

    /**
     * View or print single subscription invoice statement
     */
    public function showInvoice($id)
    {
        $invoice = POSSubscriptionInvoice::with(['tenant.mainBranch', 'subscription', 'verifier'])->findOrFail($id);
        return view('pages.sa.subscriptions.invoice_show', compact('invoice'));
    }

    /**
     * Record Manual Offline Payment / Issue Direct Subscription Invoice
     */
    public function storeManual(Request $request)
    {
        $validated = $request->validate([
            'tenant_id'         => ['required', 'exists:pos_tenants,id'],
            'subscription_id'   => ['required', 'exists:pos_subscriptions,id'],
            'amount'            => ['required', 'numeric', 'min:0'],
            'payment_method'    => ['required', 'string', 'in:cash,bank_transfer,qrph,gcash,maya,manual_sa'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'notes'             => ['nullable', 'string', 'max:500'],
            'auto_activate'     => ['nullable', 'boolean'],
        ]);

        $tenant = POSTenant::findOrFail($validated['tenant_id']);
        $plan = POSSubscription::findOrFail($validated['subscription_id']);
        $autoActivate = $request->boolean('auto_activate', true);

        $durationDays = (int) ($plan->duration_days ?? 30);
        $currentEnd = $tenant->subscription_end ? Carbon::parse($tenant->subscription_end) : null;

        if ($currentEnd && $currentEnd->isFuture()) {
            $startDate = $tenant->subscription_start ? Carbon::parse($tenant->subscription_start) : now();
            $endDate = $currentEnd->copy()->addDays($durationDays);
        } else {
            $startDate = now();
            $endDate = now()->addDays($durationDays);
        }

        $ref = !empty($validated['payment_reference']) ? trim($validated['payment_reference']) : ('SA-MANUAL-' . strtoupper(Str::random(6)));
        $verifierName = auth()->user()->name ?? 'SuperAdmin';

        DB::transaction(function () use ($tenant, $plan, $validated, $autoActivate, $startDate, $endDate, $ref, $verifierName) {
            $invoice = POSSubscriptionInvoice::create([
                'tenant_id'            => $tenant->id,
                'subscription_id'      => $plan->id,
                'plan_name'            => $plan->name,
                'billing_cycle'        => $plan->billing_cycle ?? 'monthly',
                'duration_days'        => $plan->duration_days ?? 30,
                'max_terminals'        => $plan->max_terminals ?? 1,
                'max_products'         => $plan->max_products ?? 1000,
                'amount'               => (float) $validated['amount'],
                'discount_amount'      => 0,
                'net_amount'           => (float) $validated['amount'],
                'payment_method'       => $validated['payment_method'],
                'payment_reference'    => $ref,
                'payment_sender_name'  => $tenant->owner_name,
                'payment_sender_phone' => $tenant->phone,
                'payment_status'       => $autoActivate ? 'paid' : 'pending',
                'billing_date'         => now()->toDateString(),
                'due_date'             => now()->addDays(3)->toDateString(),
                'paid_at'              => $autoActivate ? now() : null,
                'period_start'         => $autoActivate ? $startDate->toDateString() : null,
                'period_end'           => $autoActivate ? $endDate->toDateString() : null,
                'notes'                => $validated['notes'] ?? ("Manual payment recorded by {$verifierName}"),
                'verified_by'          => $autoActivate ? auth()->id() : null,
                'created_by'           => auth()->id(),
            ]);

            if ($autoActivate) {
                $tenant->update([
                    'subscription_id'    => $plan->id,
                    'pending_plan_id'    => null,
                    'payment_status'     => 'paid',
                    'status'             => 'active',
                    'paid_at'            => now(),
                    'subscription_start' => $startDate->toDateString(),
                    'subscription_end'   => $endDate->toDateString(),
                    'payment_method'     => $validated['payment_method'],
                    'payment_reference'  => $ref,
                    'payment_amount'     => (float) $validated['amount'],
                    'payment_notes'      => "Direct payment registered by {$verifierName} on " . now()->format('M d, Y h:i A'),
                ]);
            }
        });

        app(TenantSubscriptionService::class)->clearTenantCache($tenant->id);

        return redirect()->route('sa.subscriptions.billing')->with('success', "Successfully recorded invoice and billing entry for {$tenant->business_name}!");
    }

    /**
     * Approve invoice directly from the billing ledger
     */
    public function approveInvoice(Request $request, $id)
    {
        $invoice = POSSubscriptionInvoice::findOrFail($id);
        $tenant = POSTenant::findOrFail($invoice->tenant_id);
        $plan = $invoice->subscription ?: POSSubscription::find($tenant->subscription_id) ?: POSSubscription::first();

        $durationDays = (int) ($invoice->duration_days ?: ($plan->duration_days ?? 30));
        $currentEnd = $tenant->subscription_end ? Carbon::parse($tenant->subscription_end) : null;

        if ($currentEnd && $currentEnd->isFuture()) {
            $startDate = $tenant->subscription_start ? Carbon::parse($tenant->subscription_start) : now();
            $endDate = $currentEnd->copy()->addDays($durationDays);
        } else {
            $startDate = now();
            $endDate = now()->addDays($durationDays);
        }

        $verifierName = auth()->user()->name ?? 'SuperAdmin';

        DB::transaction(function () use ($invoice, $tenant, $plan, $startDate, $endDate, $verifierName) {
            $invoice->update([
                'payment_status' => 'paid',
                'paid_at'        => now(),
                'period_start'   => $startDate->toDateString(),
                'period_end'     => $endDate->toDateString(),
                'verified_by'    => auth()->id(),
                'notes'          => $invoice->notes . " | Approved by {$verifierName} on " . now()->format('M d, Y h:i A'),
            ]);

            $tenant->update([
                'subscription_id'    => $plan?->id ?: $tenant->subscription_id,
                'pending_plan_id'    => null,
                'payment_status'     => 'paid',
                'status'             => 'active',
                'paid_at'            => now(),
                'subscription_start' => $startDate->toDateString(),
                'subscription_end'   => $endDate->toDateString(),
                'payment_notes'      => "Invoice #{$invoice->invoice_no} approved by {$verifierName}",
            ]);
        });

        app(TenantSubscriptionService::class)->clearTenantCache($tenant->id);

        return redirect()->back()->with('success', "Invoice #{$invoice->invoice_no} approved! Subscription extended until {$endDate->format('M d, Y')}.");
    }

    /**
     * Reject invoice
     */
    public function rejectInvoice(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $invoice = POSSubscriptionInvoice::findOrFail($id);
        $tenant = POSTenant::findOrFail($invoice->tenant_id);
        $verifierName = auth()->user()->name ?? 'SuperAdmin';

        DB::transaction(function () use ($invoice, $tenant, $validated, $verifierName) {
            $invoice->update([
                'payment_status'   => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
                'verified_by'      => auth()->id(),
            ]);

            $tenant->update([
                'payment_status' => 'rejected',
                'payment_notes'  => $validated['rejection_reason'] . " (Reviewed by {$verifierName})",
            ]);
        });

        app(TenantSubscriptionService::class)->clearTenantCache($tenant->id);

        return redirect()->back()->with('warning', "Invoice #{$invoice->invoice_no} has been marked as rejected.");
    }

    /**
     * View complete billing & subscription ledger for a specific tenant
     */
    public function tenantHistory($tenantId)
    {
        $tenant = POSTenant::with(['subscription', 'invoices.verifier'])->findOrFail($tenantId);
        $invoices = $tenant->invoices;

        return view('pages.sa.subscriptions.tenant_billing_history', compact('tenant', 'invoices'));
    }
}
