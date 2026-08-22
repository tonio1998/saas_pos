<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\POS\POSCustomerLedger;

$rows = POSCustomerLedger::where('customer_id', 16)->get();
foreach ($rows as $r) {
    echo "ID: {$r->id} | Debit: ₱{$r->debit} | Credit: ₱{$r->credit} | Running Balance Col: ₱{$r->running_balance} | Created: {$r->created_at}\n";
}
