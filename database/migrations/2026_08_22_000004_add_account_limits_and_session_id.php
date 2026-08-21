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
        if (Schema::hasTable('pos_subscriptions')) {
            Schema::table('pos_subscriptions', function (Blueprint $table) {
                if (!Schema::hasColumn('pos_subscriptions', 'max_admin_accounts')) {
                    $table->unsignedInteger('max_admin_accounts')->default(1)->after('max_users');
                }
                if (!Schema::hasColumn('pos_subscriptions', 'max_cashier_accounts')) {
                    $table->unsignedInteger('max_cashier_accounts')->default(1)->after('max_admin_accounts');
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'current_session_id')) {
                    $table->string('current_session_id')->nullable()->after('remember_token');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pos_subscriptions')) {
            Schema::table('pos_subscriptions', function (Blueprint $table) {
                if (Schema::hasColumn('pos_subscriptions', 'max_admin_accounts')) {
                    $table->dropColumn('max_admin_accounts');
                }
                if (Schema::hasColumn('pos_subscriptions', 'max_cashier_accounts')) {
                    $table->dropColumn('max_cashier_accounts');
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'current_session_id')) {
                    $table->dropColumn('current_session_id');
                }
            });
        }
    }
};
