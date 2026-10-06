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
            if (!Schema::hasColumn('pos_tenants', 'last_reminder_sent_at')) {
                $table->timestamp('last_reminder_sent_at')->nullable()->after('payment_notes');
            }
            if (!Schema::hasColumn('pos_tenants', 'reminder_count')) {
                $table->unsignedInteger('reminder_count')->default(0)->after('last_reminder_sent_at');
            }
            if (!Schema::hasColumn('pos_tenants', 'last_reminder_channel')) {
                $table->string('last_reminder_channel', 50)->nullable()->after('reminder_count');
            }
            if (!Schema::hasColumn('pos_tenants', 'last_reminder_notes')) {
                $table->text('last_reminder_notes')->nullable()->after('last_reminder_channel');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_tenants', function (Blueprint $table) {
            $table->dropColumn([
                'last_reminder_sent_at',
                'reminder_count',
                'last_reminder_channel',
                'last_reminder_notes',
            ]);
        });
    }
};
