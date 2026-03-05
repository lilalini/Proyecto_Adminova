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
        Schema::create('distribution_channels', function (Blueprint $table) {
            $table->id();
            $table->string('channel_code')->unique(); // booking, airbnb, expedia
            $table->string('name');
            $table->enum('channel_type', ['OTA', 'direct', 'corporate', 'referral'])->default('direct');
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->json('api_config')->nullable(); // credenciales, endpoints
            $table->boolean('is_active')->default(true);
            $table->boolean('sync_enabled')->default(true);
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('channel_type');
            $table->index('is_active');
            $table->index('sync_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribution_channels');
    }
};
