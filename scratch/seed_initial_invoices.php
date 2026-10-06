<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenants = \App\Models\POS\POSTenant::with('subscription')->get();
$created = 0;

foreach ($tenants as $tenant) {
    if (!$tenant->subscription_id) {
        continue;
    }
    
    $exists = \App\Models\POS\POSSubscriptionInvoice::where('tenant_id', $tenant->id)->exists();
    if ($exists) {
        continue;
    }

    $sub = $tenant->subscription;
    $amount = (float)($tenant->payment_amount > 0 ? $tenant->payment_amount : ($sub?->price ?? 600));
    $status = ($tenant->payment_status === 'paid' || $tenant->status === 'active') ? 'paid' : 'pending';

    \App\Models\POS\POSSubscriptionInvoice::create([
        'tenant_id'            => $tenant->id,
        'subscription_id'      => $sub?->id,
        'plan_name'            => $sub?->name ?? 'Standard Plan',
        'billing_cycle'        => $sub?->billing_cycle ?? 'monthly',
        'duration_days'        => $sub?->duration_days ?? 30,
        'max_terminals'        => $sub?->max_terminals ?? 1,
        'max_products'         => $sub?->max_products ?? 1000,
        'amount'               => $amount,
        'discount_amount'      => 0,
        'net_amount'           => $amount,
        'payment_method'       => $tenant->payment_method ?? 'qrph',
        'payment_reference'    => $tenant->payment_reference ?? ('REF-' . strtoupper(\Illuminate\Support\Str::random(8))),
        'payment_proof'        => $tenant->payment_proof,
        'payment_sender_name'  => $tenant->payment_sender_name ?? $tenant->owner_name,
        'payment_sender_phone' => $tenant->payment_sender_phone ?? $tenant->phone,
        'payment_status'       => $status,
        'billing_date'         => $tenant->subscription_start ? \Carbon\Carbon::parse($tenant->subscription_start)->toDateString() : now()->subDays(15)->toDateString(),
        'due_date'             => $tenant->subscription_end ? \Carbon\Carbon::parse($tenant->subscription_end)->toDateString() : now()->addDays(15)->toDateString(),
        'paid_at'              => $status === 'paid' ? ($tenant->paid_at ?? now()->subDays(15)) : null,
        'period_start'         => $tenant->subscription_start ? \Carbon\Carbon::parse($tenant->subscription_start)->toDateString() : now()->subDays(15)->toDateString(),
        'period_end'           => $tenant->subscription_end ? \Carbon\Carbon::parse($tenant->subscription_end)->toDateString() : now()->addDays(15)->toDateString(),
        'notes'                => 'Initial system subscription onboarding invoice',
    ]);
    $created++;
}

echo "Backfilled {$created} subscription invoices for existing tenants.\n";
