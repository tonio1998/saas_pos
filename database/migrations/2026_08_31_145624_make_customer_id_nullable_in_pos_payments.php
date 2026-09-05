<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Make customer_id nullable in pos_payments so walk-in sales
     * (no customer linked) can be saved without a constraint violation.
     */
    public function up(): void
    {
        Schema::table('pos_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pos_payments', function (Blueprint $table) {
            // Note: reverting to NOT NULL may fail if NULL rows exist
            $table->unsignedBigInteger('customer_id')->nullable(false)->change();
        });
    }
};
