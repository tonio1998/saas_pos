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
        if (Schema::hasTable('pos_sales')) {
            Schema::table('pos_sales', function (Blueprint $table) {
                if (!Schema::hasColumn('pos_sales', 'vatable_sales')) {
                    $table->decimal('vatable_sales', 12, 2)->default(0)->after('subtotal');
                }
                if (!Schema::hasColumn('pos_sales', 'vat_exempt_sales')) {
                    $table->decimal('vat_exempt_sales', 12, 2)->default(0)->after('vatable_sales');
                }
                if (!Schema::hasColumn('pos_sales', 'zero_rated_sales')) {
                    $table->decimal('zero_rated_sales', 12, 2)->default(0)->after('vat_exempt_sales');
                }
                if (!Schema::hasColumn('pos_sales', 'tendered_amount')) {
                    $table->decimal('tendered_amount', 12, 2)->default(0)->after('total_amount');
                }
                if (!Schema::hasColumn('pos_sales', 'change_amount')) {
                    $table->decimal('change_amount', 12, 2)->default(0)->after('tendered_amount');
                }
                if (!Schema::hasColumn('pos_sales', 'discount_mode')) {
                    $table->string('discount_mode', 50)->nullable()->after('discount_type');
                }
                if (!Schema::hasColumn('pos_sales', 'discount_value')) {
                    $table->decimal('discount_value', 12, 2)->default(0)->after('discount_mode');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pos_sales')) {
            Schema::table('pos_sales', function (Blueprint $table) {
                $columns = [
                    'vatable_sales',
                    'vat_exempt_sales',
                    'zero_rated_sales',
                    'tendered_amount',
                    'change_amount',
                    'discount_mode',
                    'discount_value'
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('pos_sales', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
