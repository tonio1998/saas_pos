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
                if (!Schema::hasColumn('users', 'otp_code')) {
                    $table->string('otp_code', 10)->nullable()->after('remember_token');
                }
                if (!Schema::hasColumn('users', 'otp_expires_at')) {
                    $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
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
                if (Schema::hasColumn('users', 'otp_code')) {
                    $table->dropColumn('otp_code');
                }
                if (Schema::hasColumn('users', 'otp_expires_at')) {
                    $table->dropColumn('otp_expires_at');
                }
            });
        }
    }
};
