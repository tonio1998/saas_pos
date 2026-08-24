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
        if (Schema::hasTable('pos_promotions') && !Schema::hasColumn('pos_promotions', 'target_ids')) {
            Schema::table('pos_promotions', function (Blueprint $table) {
                $table->json('target_ids')->nullable()->after('target_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pos_promotions') && Schema::hasColumn('pos_promotions', 'target_ids')) {
            Schema::table('pos_promotions', function (Blueprint $table) {
                $table->dropColumn('target_ids');
            });
        }
    }
};
