<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_product_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->string('variant_name');               // e.g. "2.5kg Pack", "5kg Sack"
            $table->string('barcode')->nullable();
            $table->string('sku')->nullable();
            $table->decimal('qty_per_pack', 10, 4)->default(1); // multiplier from base unit
            $table->unsignedBigInteger('unit_id')->nullable();  // override unit (e.g. Pack, Sack)
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->decimal('selling_price', 12, 2)->default(0);
            $table->decimal('wholesale_price', 12, 2)->nullable();
            $table->decimal('stock_on_hand', 10, 4)->default(0);
            $table->integer('reorder_level')->default(0);
            $table->string('status')->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_id')->references('id')->on('pos_products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_product_variants');
    }
};
