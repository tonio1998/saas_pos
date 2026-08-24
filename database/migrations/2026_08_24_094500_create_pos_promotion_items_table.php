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
        Schema::create('pos_promotion_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('promotion_id')->index();
            $table->enum('item_type', ['product', 'category', 'variant'])->default('product');
            $table->unsignedBigInteger('item_id')->index();
            $table->decimal('custom_discount_value', 12, 2)->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'promotion_id']);
            $table->index(['promotion_id', 'item_type', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_promotion_items');
    }
};
