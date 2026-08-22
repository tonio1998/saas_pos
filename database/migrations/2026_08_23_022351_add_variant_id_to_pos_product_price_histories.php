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
        if (Schema::hasTable('pos_product_price_histories') && !Schema::hasColumn('pos_product_price_histories', 'variant_id')) {
            Schema::table('pos_product_price_histories', function (Blueprint $table) {
                $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
                $table->index('variant_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pos_product_price_histories') && Schema::hasColumn('pos_product_price_histories', 'variant_id')) {
            Schema::table('pos_product_price_histories', function (Blueprint $table) {
                $table->dropIndex(['variant_id']);
                $table->dropColumn('variant_id');
            });
        }
    }
};
