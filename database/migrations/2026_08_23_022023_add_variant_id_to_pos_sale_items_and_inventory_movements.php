<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('pos_sale_items') && !Schema::hasColumn('pos_sale_items', 'variant_id')) {
            Schema::table('pos_sale_items', function (Blueprint $table) {
                $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
            });
        }

        if (Schema::hasTable('pos_inventory_movements') && !Schema::hasColumn('pos_inventory_movements', 'variant_id')) {
            Schema::table('pos_inventory_movements', function (Blueprint $table) {
                $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
            });
        }

        if (Schema::hasTable('pos_stock_transactions') && !Schema::hasColumn('pos_stock_transactions', 'variant_id')) {
            Schema::table('pos_stock_transactions', function (Blueprint $table) {
                $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pos_sale_items') && Schema::hasColumn('pos_sale_items', 'variant_id')) {
            Schema::table('pos_sale_items', function (Blueprint $table) {
                $table->dropColumn('variant_id');
            });
        }

        if (Schema::hasTable('pos_inventory_movements') && Schema::hasColumn('pos_inventory_movements', 'variant_id')) {
            Schema::table('pos_inventory_movements', function (Blueprint $table) {
                $table->dropColumn('variant_id');
            });
        }

        if (Schema::hasTable('pos_stock_transactions') && Schema::hasColumn('pos_stock_transactions', 'variant_id')) {
            Schema::table('pos_stock_transactions', function (Blueprint $table) {
                $table->dropColumn('variant_id');
            });
        }
    }
};
