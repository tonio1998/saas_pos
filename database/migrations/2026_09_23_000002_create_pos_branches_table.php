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
        if (!Schema::hasTable('pos_branches')) {
            Schema::create('pos_branches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('pos_tenants')->onDelete('cascade');
                $table->string('branch_name', 150);
                $table->string('branch_code', 50)->default('MAIN');
                $table->text('address')->nullable();
                $table->string('phone', 50)->nullable();
                $table->string('email', 150)->nullable();
                $table->string('manager_name', 150)->nullable();
                $table->boolean('is_main')->default(true);
                $table->string('status', 30)->default('active');
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_branches');
    }
};
