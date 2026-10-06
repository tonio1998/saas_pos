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
        Schema::create('pos_subscription_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no', 60)->unique()->index();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('subscription_id')->nullable()->index();
            
            // Plan snapshot at billing time
            $table->string('plan_name', 120);
            $table->string('billing_cycle', 40)->default('monthly');
            $table->integer('duration_days')->default(30);
            $table->integer('max_terminals')->default(1);
            $table->integer('max_products')->default(1000);
            
            // Financials
            $table->decimal('amount', 12, 2)->default(0.00);
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->decimal('net_amount', 12, 2)->default(0.00);
            $table->string('currency', 10)->default('PHP');
            
            // Payment details
            $table->string('payment_method', 50)->nullable(); // qrph, gcash, maya, bank_transfer, cash, manual_sa
            $table->string('payment_reference', 120)->nullable()->index();
            $table->string('payment_proof', 255)->nullable();
            $table->string('payment_sender_name', 255)->nullable();
            $table->string('payment_sender_phone', 60)->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'overdue', 'rejected', 'cancelled'])->default('pending')->index();
            
            // Dates & Timestamps
            $table->date('billing_date');
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            
            // Administrative & Audit
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('pos_tenants')->onDelete('cascade');
            $table->foreign('subscription_id')->references('id')->on('pos_subscriptions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_subscription_invoices');
    }
};
