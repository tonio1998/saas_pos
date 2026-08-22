<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\POS\POSCustomers;
use App\Models\POS\POSCustomerLedger;

$customers = POSCustomers::all();
foreach ($customers as $c) {
    $rows = POSCustomerLedger::where('customer_id', $c->id)->orderBy('id', 'asc')->get();
    $running = 0;
    foreach ($rows as $r) {
        $running += ((float)$r->debit - (float)$r->credit);
        $r->running_balance = max(0, $running);
        $r->save();
    }
}

echo "Successfully re-calculated all customer ledger running balances!\n";
