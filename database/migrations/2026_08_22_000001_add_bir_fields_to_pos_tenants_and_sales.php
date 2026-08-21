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
        if (Schema::hasTable('pos_tenants')) {
            Schema::table('pos_tenants', function (Blueprint $table) {
                if (!Schema::hasColumn('pos_tenants', 'tin')) {
                    $table->string('tin', 50)->nullable()->after('address');
                }
                if (!Schema::hasColumn('pos_tenants', 'branch_code')) {
                    $table->string('branch_code', 20)->default('00000')->after('tin');
                }
                if (!Schema::hasColumn('pos_tenants', 'bir_acc_no')) {
                    $table->string('bir_acc_no', 100)->nullable()->after('branch_code');
                }
                if (!Schema::hasColumn('pos_tenants', 'bir_acc_date')) {
                    $table->date('bir_acc_date')->nullable()->after('bir_acc_no');
                }
                if (!Schema::hasColumn('pos_tenants', 'bir_min')) {
                    $table->string('bir_min', 100)->nullable()->after('bir_acc_date');
                }
                if (!Schema::hasColumn('pos_tenants', 'bir_sn')) {
                    $table->string('bir_sn', 100)->nullable()->after('bir_min');
                }
                if (!Schema::hasColumn('pos_tenants', 'header_text')) {
                    $table->text('header_text')->nullable()->after('bir_sn');
                }
                if (!Schema::hasColumn('pos_tenants', 'footer_text')) {
                    $table->text('footer_text')->nullable()->after('header_text');
                }
                if (!Schema::hasColumn('pos_tenants', 'payment_status')) {
                    $table->string('payment_status', 30)->default('pending')->after('status');
                }
                if (!Schema::hasColumn('pos_tenants', 'payment_reference')) {
                    $table->string('payment_reference', 100)->nullable()->after('payment_status');
                }
                if (!Schema::hasColumn('pos_tenants', 'paid_at')) {
                    $table->timestamp('paid_at')->nullable()->after('payment_reference');
                }
            });
        }

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
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pos_tenants')) {
            Schema::table('pos_tenants', function (Blueprint $table) {
                $table->dropColumn([
                    'tin', 'branch_code', 'bir_acc_no', 'bir_acc_date',
                    'bir_min', 'bir_sn', 'header_text', 'footer_text',
                    'payment_status', 'payment_reference', 'paid_at'
                ]);
            });
        }

        if (Schema::hasTable('pos_sales')) {
            Schema::table('pos_sales', function (Blueprint $table) {
                $table->dropColumn([
                    'vatable_sales', 'vat_exempt_sales', 'zero_rated_sales',
                    'tendered_amount', 'change_amount'
                ]);
            });
        }
    }
};
