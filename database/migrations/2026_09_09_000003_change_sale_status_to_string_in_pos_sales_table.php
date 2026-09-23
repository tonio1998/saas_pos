<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('pos_sales')) {
            // Using raw SQL to ensure compatibility with MySQL enum alteration without requiring doctrine/dbal
            DB::statement("ALTER TABLE `pos_sales` MODIFY COLUMN `sale_status` VARCHAR(50) NOT NULL DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pos_sales')) {
            DB::statement("ALTER TABLE `pos_sales` MODIFY COLUMN `sale_status` ENUM('pending','completed','voided','refunded','refund','partial_refund') NOT NULL DEFAULT 'pending'");
        }
    }
};
