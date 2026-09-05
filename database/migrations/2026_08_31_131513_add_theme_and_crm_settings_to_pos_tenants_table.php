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
            if (!Schema::hasColumn('pos_tenants', 'theme_settings')) {
                $table->json('theme_settings')->nullable()->after('footer_text');
            }
            if (!Schema::hasColumn('pos_tenants', 'crm_settings')) {
                $table->json('crm_settings')->nullable()->after('theme_settings');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_tenants', function (Blueprint $table) {
            if (Schema::hasColumn('pos_tenants', 'theme_settings')) {
                $table->dropColumn('theme_settings');
            }
            if (Schema::hasColumn('pos_tenants', 'crm_settings')) {
                $table->dropColumn('crm_settings');
            }
        });
    }
};
