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
        Schema::dropIfExists('pos_promotions');

        Schema::create('pos_promotions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('title', 190);
            $table->string('promo_code', 50)->nullable()->index();
            $table->enum('promo_type', ['percentage', 'fixed_amount', 'bulk_tier', 'buy_x_get_y'])->default('percentage');
            $table->decimal('discount_value', 12, 2)->default(0); // e.g. 10 (%) or 50.00 (PHP) or bulk special price
            $table->decimal('min_spend', 12, 2)->default(0);      // Min cart total required
            $table->integer('min_quantity')->default(1);          // Min qty required (e.g. 10 for bulk)
            $table->integer('get_quantity')->default(0);          // For buy X get Y (e.g. buy 3 get 1 free)
            $table->string('applies_to', 30)->default('all');     // 'all', 'category', 'product', 'variant'
            $table->unsignedBigInteger('target_id')->nullable();  // Category ID, Product ID, or Variant ID
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->integer('usage_count')->default(0);
            $table->integer('usage_limit')->nullable();           // Max times promo can be used
            $table->text('description')->nullable();
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
        Schema::dropIfExists('pos_promotions');
    }
};
