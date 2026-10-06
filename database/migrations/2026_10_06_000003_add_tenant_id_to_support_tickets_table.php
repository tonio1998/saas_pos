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
        Schema::table('support_tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('support_tickets', 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')->nullable()->after('id')->index();
            }
            if (!Schema::hasColumn('support_tickets', 'contact_phone')) {
                $table->string('contact_phone', 50)->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('support_tickets', 'callback_requested')) {
                $table->boolean('callback_requested')->default(false)->after('contact_phone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            if (Schema::hasColumn('support_tickets', 'callback_requested')) {
                $table->dropColumn('callback_requested');
            }
            if (Schema::hasColumn('support_tickets', 'contact_phone')) {
                $table->dropColumn('contact_phone');
            }
            if (Schema::hasColumn('support_tickets', 'tenant_id')) {
                $table->dropColumn('tenant_id');
            }
        });
    }
};
