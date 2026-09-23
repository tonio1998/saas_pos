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
            if (!Schema::hasColumn('pos_tenants', 'logo_square')) {
                $table->string('logo_square', 255)->nullable()->after('logo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_tenants', function (Blueprint $table) {
            if (Schema::hasColumn('pos_tenants', 'logo_square')) {
                $table->dropColumn('logo_square');
            }
        });
    }
};
