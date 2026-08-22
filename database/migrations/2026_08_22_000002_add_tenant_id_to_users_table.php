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
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'tenant_id')) {
                    $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('users', 'username')) {
                    $table->string('username')->nullable()->after('name');
                }
                if (!Schema::hasColumn('users', 'verified')) {
                    $table->tinyInteger('verified')->default(1)->after('remember_token');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'tenant_id')) {
                    $table->dropColumn('tenant_id');
                }
                if (Schema::hasColumn('users', 'username')) {
                    $table->dropColumn('username');
                }
                if (Schema::hasColumn('users', 'verified')) {
                    $table->dropColumn('verified');
                }
            });
        }
    }
};
