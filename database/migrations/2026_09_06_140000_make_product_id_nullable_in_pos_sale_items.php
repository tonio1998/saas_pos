<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Make product_id nullable in pos_sale_items so custom services,
     * ad-hoc fees (e.g. Tote Bag, Delivery, Broken Items) can be recorded.
     */
    public function up(): void
    {
        Schema::table('pos_sale_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pos_sale_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
        });
    }
};
