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
        Schema::create('system_settings', function (Blueprint $table) {

            $table->id();

            $table->longText('cacert_path')->nullable();

            $table->longText('python_path')->nullable();

            $table->string('port_com', 20)->nullable();

            $table->integer('sms_failed_count')
                ->nullable()
                ->default(0);

            $table->integer('sms_low_balance')
                ->nullable()
                ->default(0);

            $table->integer('total_sent')
                ->nullable()
                ->default(0);

            $table->string('sms_provider', 20)
                ->default('api');

            $table->longText('sms_api_url')->nullable();

            $table->longText('sms_api_key')->nullable();

            $table->longText('sms_api_device_id')->nullable();

            $table->dateTime('sms_last_failed_at')
                ->nullable();

            $table->boolean('gsm_enabled')
                ->default(false);

            $table->boolean('sms_enabled')
                ->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
