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
        // 1. Add expiry_date to pos_stocks
        if (Schema::hasTable('pos_stocks') && !Schema::hasColumn('pos_stocks', 'expiry_date')) {
            Schema::table('pos_stocks', function (Blueprint $table) {
                $table->date('expiry_date')->nullable()->after('quantity')->index();
            });
        }

        // 2. Add discount metadata to pos_sales
        if (Schema::hasTable('pos_sales')) {
            Schema::table('pos_sales', function (Blueprint $table) {
                if (!Schema::hasColumn('pos_sales', 'discount_type')) {
                    $table->string('discount_type', 50)->nullable()->after('discount_amount'); // 'manual', 'promo_code', 'sc_pwd', 'bulk_tier'
                }
                if (!Schema::hasColumn('pos_sales', 'discount_reference')) {
                    $table->string('discount_reference', 100)->nullable()->after('discount_type'); // e.g. OSCA-12345 or PROMO-SUMMER
                }
                if (!Schema::hasColumn('pos_sales', 'promo_id')) {
                    $table->unsignedBigInteger('promo_id')->nullable()->after('discount_reference');
                }
            });
        }

        // 3. Add discount details to pos_sale_items
        if (Schema::hasTable('pos_sale_items')) {
            Schema::table('pos_sale_items', function (Blueprint $table) {
                if (!Schema::hasColumn('pos_sale_items', 'discount_amount')) {
                    $table->decimal('discount_amount', 12, 2)->default(0)->after('subtotal');
                }
                if (!Schema::hasColumn('pos_sale_items', 'promo_id')) {
                    $table->unsignedBigInteger('promo_id')->nullable()->after('discount_amount');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pos_stocks') && Schema::hasColumn('pos_stocks', 'expiry_date')) {
            Schema::table('pos_stocks', function (Blueprint $table) {
                $table->dropColumn('expiry_date');
            });
        }

        if (Schema::hasTable('pos_sales')) {
            Schema::table('pos_sales', function (Blueprint $table) {
                $table->dropColumn(['discount_type', 'discount_reference', 'promo_id']);
            });
        }

        if (Schema::hasTable('pos_sale_items')) {
            Schema::table('pos_sale_items', function (Blueprint $table) {
                $table->dropColumn(['discount_amount', 'promo_id']);
            });
        }
    }
};
