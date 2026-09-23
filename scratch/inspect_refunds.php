<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$refunds = App\Models\POS\POSSale::where('tenant_id', 2)->where('sale_status', 'refund')->get();
foreach ($refunds as $r) {
    echo "Refund ID: {$r->id} | Invoice: {$r->invoice_no} | Date: {$r->sale_date} | Total: {$r->total_amount}\n";
    $movements = App\Models\POS\InventoryMovement::where('reference_id', $r->id)->get();
    foreach ($movements as $m) {
        echo "  Movement ID: {$m->id} | Product ID: {$m->product_id} | Qty: {$m->qty}\n";
    }
}
