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
                if (!Schema::hasColumn('pos_subscriptions', 'is_promo')) {
                    $table->boolean('is_promo')->default(false)->after('price');
                }
                if (!Schema::hasColumn('pos_subscriptions', 'promo_price')) {
                    $table->decimal('promo_price', 10, 2)->nullable()->after('is_promo');
                }
                if (!Schema::hasColumn('pos_subscriptions', 'promo_code')) {
                    $table->string('promo_code', 50)->nullable()->after('promo_price');
                }
                if (!Schema::hasColumn('pos_subscriptions', 'badge_text')) {
                    $table->string('badge_text', 100)->nullable()->after('promo_code');
                }
                if (!Schema::hasColumn('pos_subscriptions', 'promo_expires_at')) {
                    $table->timestamp('promo_expires_at')->nullable()->after('badge_text');
                }
                if (!Schema::hasColumn('pos_subscriptions', 'featured')) {
                    $table->boolean('featured')->default(false)->after('promo_expires_at');
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
                $columns = ['is_promo', 'promo_price', 'promo_code', 'badge_text', 'promo_expires_at', 'featured'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('pos_subscriptions', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
