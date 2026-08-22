<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\POS\POSCustomers;
use App\Models\POS\POSCustomerLedger;

$customers = POSCustomers::with('credit')->get();
foreach ($customers as $c) {
    $sumBalance = (float) POSCustomerLedger::where('customer_id', $c->id)->sum(\DB::raw('debit - credit'));
    $latestRunning = (float) ($c->credit?->running_balance ?? 0);
    echo "Customer ID: {$c->id} | Name: {$c->CustomerName}\n";
    echo "  Sum (debit - credit): ₱" . number_format($sumBalance, 2) . "\n";
    echo "  Latest running_balance: ₱" . number_format($latestRunning, 2) . "\n";
    echo "---------------------------------------------------------\n";
}
