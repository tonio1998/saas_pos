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
        Schema::table('pos_tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('pos_tenants', 'currency_symbol')) {
                $table->string('currency_symbol', 10)->default('₱')->after('header_text');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_tenants', function (Blueprint $table) {
            if (Schema::hasColumn('pos_tenants', 'currency_symbol')) {
                $table->dropColumn('currency_symbol');
            }
        });
    }
};
