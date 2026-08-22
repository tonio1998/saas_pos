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
        if (Schema::hasTable('pos_inventory_movements') && Schema::hasColumn('pos_inventory_movements', 'reference_id')) {
            Schema::table('pos_inventory_movements', function (Blueprint $table) {
                $table->unsignedBigInteger('reference_id')->nullable()->default(0)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
