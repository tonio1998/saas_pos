<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

DB::transaction(function() {
    $dupRefund = App\Models\POS\POSSale::find(379);
    if ($dupRefund) {
        // Reverse inventory restock
        foreach ($dupRefund->items as $item) {
            $product = App\Models\POS\POSProducts::find($item->product_id);
            if ($product) {
                $qty = abs($item->qty);
                $variant = $item->variant_id ? $product->variants()->find($item->variant_id) : null;
                if ($variant) {
                    $variant->decrement('stock_on_hand', $qty);
                    $product->decrement('stock_on_hand', $qty * ($variant->qty_per_pack ?? 1));
                } else {
                    $product->decrement('stock_on_hand', $qty);
                }
            }
        }

        // Delete movements
        App\Models\POS\InventoryMovement::where('reference_id', 379)->forceDelete();
        // Delete payments
        App\Models\POS\POSPayment::where('sale_id', 379)->forceDelete();
        // Delete cash movements
        App\Models\POS\POSCashMovement::where('reference_no', $dupRefund->invoice_no)->forceDelete();
        // Delete sale items
        App\Models\POS\POSSaleItem::where('sale_id', 379)->forceDelete();
        // Delete refund sale
        $dupRefund->forceDelete();

        echo "Duplicate refund 379 removed and excess restock reversed successfully.\n";
    }
});
