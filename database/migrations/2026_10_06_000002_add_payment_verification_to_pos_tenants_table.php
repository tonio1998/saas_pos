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
            if (!Schema::hasColumn('pos_tenants', 'pending_plan_id')) {
                $table->unsignedBigInteger('pending_plan_id')->nullable()->after('subscription_id');
            }
            if (!Schema::hasColumn('pos_tenants', 'payment_method')) {
                $table->string('payment_method', 50)->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('pos_tenants', 'payment_proof')) {
                $table->string('payment_proof', 255)->nullable()->after('payment_reference');
            }
            if (!Schema::hasColumn('pos_tenants', 'payment_sender_name')) {
                $table->string('payment_sender_name', 255)->nullable()->after('payment_proof');
            }
            if (!Schema::hasColumn('pos_tenants', 'payment_sender_phone')) {
                $table->string('payment_sender_phone', 50)->nullable()->after('payment_sender_name');
            }
            if (!Schema::hasColumn('pos_tenants', 'payment_amount')) {
                $table->decimal('payment_amount', 10, 2)->nullable()->after('payment_sender_phone');
            }
            if (!Schema::hasColumn('pos_tenants', 'payment_submitted_at')) {
                $table->dateTime('payment_submitted_at')->nullable()->after('paid_at');
            }
            if (!Schema::hasColumn('pos_tenants', 'payment_notes')) {
                $table->text('payment_notes')->nullable()->after('payment_submitted_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_tenants', function (Blueprint $table) {
            $columns = [
                'pending_plan_id',
                'payment_method',
                'payment_proof',
                'payment_sender_name',
                'payment_sender_phone',
                'payment_amount',
                'payment_submitted_at',
                'payment_notes',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('pos_tenants', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
