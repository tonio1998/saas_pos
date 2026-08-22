<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\POS\POSCustomerLedger;
use Illuminate\Support\Facades\DB;

// Clean up exact duplicate seeded rows in pos_customer_ledgers
$allLedgers = POSCustomerLedger::all();
echo "Total ledger rows before cleaning: " . $allLedgers->count() . "\n";

$duplicates = DB::table('pos_customer_ledgers')
    ->select('customer_id', 'debit', 'credit', 'created_at', DB::raw('MIN(id) as keep_id'), DB::raw('COUNT(*) as cnt'))
    ->groupBy('customer_id', 'debit', 'credit', 'created_at')
    ->havingRaw('COUNT(*) > 1')
    ->get();

foreach ($duplicates as $dup) {
    echo "Duplicate found: Customer {$dup->customer_id} | Debit: ₱{$dup->debit} | Credit: ₱{$dup->credit} | Count: {$dup->cnt} | Keeping ID: {$dup->keep_id}\n";
    
    DB::table('pos_customer_ledgers')
        ->where('customer_id', $dup->customer_id)
        ->where('debit', $dup->debit)
        ->where('credit', $dup->credit)
        ->where('created_at', $dup->created_at)
        ->where('id', '!=', $dup->keep_id)
        ->delete();
}

$afterCount = DB::table('pos_customer_ledgers')->count();
echo "Total ledger rows after removing duplicate seeders: {$afterCount}\n";

// Recalculate running balance per customer
$customers = DB::table('pos_customer_ledgers')->select('customer_id')->distinct()->get();
foreach ($customers as $c) {
    $rows = POSCustomerLedger::where('customer_id', $c->customer_id)->orderBy('id', 'asc')->get();
    $running = 0;
    foreach ($rows as $r) {
        $running += ((float)$r->debit - (float)$r->credit);
        $r->running_balance = max(0, $running);
        $r->save();
    }
    $finalBal = (float)POSCustomerLedger::where('customer_id', $c->customer_id)->sum(DB::raw('debit - credit'));
    echo "Customer {$c->customer_id}: Final True Balance = ₱" . number_format($finalBal, 2) . "\n";
}
