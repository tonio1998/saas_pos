<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING BILLING & INVOICES SYSTEM ===\n";

// 1. Verify Routes
echo "1. Checking routes...\n";
echo "   SA Billing: " . route('sa.subscriptions.billing') . "\n";
echo "   Tenant Billing: " . route('subscription.billing') . "\n";

// 2. Fetch an invoice
$invoice = \App\Models\POS\POSSubscriptionInvoice::with('tenant')->first();
if (!$invoice) {
    die("ERROR: No subscription invoice found in DB.\n");
}
echo "   Found Invoice #{$invoice->invoice_no} for tenant {$invoice->tenant?->business_name} (ID {$invoice->tenant_id})\n";
echo "   SA Invoice View: " . route('sa.subscriptions.billing.invoice', $invoice->id) . "\n";
echo "   Tenant Invoice View: " . route('subscription.billing.invoice', $invoice->id) . "\n";
echo "   SA Tenant History: " . route('sa.subscriptions.billing.tenant', $invoice->tenant_id) . "\n";

// 3. Render SA Billing View
echo "2. Rendering SA Billing View (pages.sa.subscriptions.billing)...\n";
$invoices = \App\Models\POS\POSSubscriptionInvoice::with(['tenant', 'subscription', 'verifier'])->paginate(15);
$saHtml = view('pages.sa.subscriptions.billing', [
    'invoices' => $invoices,
    'tab' => 'all',
    'search' => '',
    'dateFrom' => null,
    'dateTo' => null,
    'totalCollected' => 10000.00,
    'thisMonthCollected' => 5000.00,
    'pendingAmount' => 600.00,
    'pendingCount' => 1,
    'overdueCount' => 0,
    'allCount' => 13,
    'paidCount' => 12,
    'rejectedCount' => 0,
    'activePlans' => \App\Models\POS\POSSubscription::all(),
    'tenantsList' => \App\Models\POS\POSTenant::all(),
])->render();
echo "   Rendered SA Billing View successfully! Length: " . strlen($saHtml) . " bytes\n";

// 4. Render SA Invoice Show View
echo "3. Rendering SA Invoice Show View (pages.sa.subscriptions.invoice_show)...\n";
$saInvHtml = view('pages.sa.subscriptions.invoice_show', ['invoice' => $invoice])->render();
echo "   Rendered SA Invoice View successfully! Length: " . strlen($saInvHtml) . " bytes\n";

// 5. Render SA Tenant History View
echo "4. Rendering SA Tenant History View (pages.sa.subscriptions.tenant_billing_history)...\n";
$tenant = $invoice->tenant;
$saHistHtml = view('pages.sa.subscriptions.tenant_billing_history', [
    'tenant' => $tenant,
    'invoices' => $tenant->invoices,
])->render();
echo "   Rendered SA Tenant History View successfully! Length: " . strlen($saHistHtml) . " bytes\n";

// 6. Render Tenant Billing Hub View
echo "5. Rendering Tenant Billing Hub View (pages.subscription.billing)...\n";
$tenantHtml = view('pages.subscription.billing', [
    'tenant' => $tenant,
    'currentTerminalsCount' => 1,
    'currentProductsCount' => 25,
    'daysRemaining' => 20,
    'isOverdue' => false,
    'isExpiringSoon' => false,
    'progressPercent' => 33,
    'availablePlans' => \App\Models\POS\POSSubscription::all(),
    'pendingInvoice' => null,
    'invoices' => $tenant->invoices,
])->render();
echo "   Rendered Tenant Billing Hub View successfully! Length: " . strlen($tenantHtml) . " bytes\n";

// 7. Render Tenant Invoice Show View
echo "6. Rendering Tenant Invoice Statement (pages.subscription.invoice_show)...\n";
$tenantInvHtml = view('pages.subscription.invoice_show', ['invoice' => $invoice])->render();
echo "   Rendered Tenant Invoice Statement successfully! Length: " . strlen($tenantInvHtml) . " bytes\n";

echo "\nALL BILLING & SUBSCRIPTION HISTORY VIEWS VALIDATED SUCCESSFULLY!\n";
