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
        Schema::dropIfExists('pos_expenses');

        Schema::create('pos_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('expense_code', 50)->nullable()->index();
            $table->date('expense_date')->index();
            $table->string('category', 50)->default('other')->index();
            $table->string('title', 255);
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('payment_method', 50)->default('cash');
            $table->string('payee', 190)->nullable();
            $table->string('reference_no', 100)->nullable();
            $table->text('notes')->nullable();
            $table->string('attachment', 255)->nullable();
            $table->unsignedBigInteger('drawer_id')->nullable();
            $table->unsignedBigInteger('cash_shift_id')->nullable();
            $table->string('status', 30)->default('approved');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_expenses');
    }
};
