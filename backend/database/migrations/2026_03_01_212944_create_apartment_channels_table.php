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
        Schema::create('apartment_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_id')->constrained()->restrictOnDelete();
            $table->foreignId('distribution_channel_id')->constrained()->restrictOnDelete();
            
            $table->string('external_listing_id')->nullable(); // ID en Booking/Airbnb
            $table->string('external_url')->nullable();
            
            $table->enum('connection_status', ['connected', 'disconnected', 'error'])->default('disconnected');
            $table->boolean('sync_enabled')->default(true);
            $table->boolean('sync_price')->default(true);
            $table->boolean('sync_availability')->default(true);
            $table->boolean('sync_content')->default(false);
            
            $table->enum('price_adjustment_type', ['percentage', 'fixed', 'none'])->default('none');
            $table->decimal('price_adjustment_value', 10, 2)->nullable();
            $table->integer('min_stay_adjustment')->nullable();
            
            $table->timestamp('last_sync_at')->nullable();
            $table->string('last_sync_status')->nullable(); // success, error, pending
            $table->text('last_sync_message')->nullable();
            
            $table->json('channel_data')->nullable(); // datos específicos del canal
            
            $table->timestamps();
            $table->softDeletes();
            
            // Un accommodation no puede estar dos veces en el mismo canal
            $table->unique(['accommodation_id', 'distribution_channel_id'], 'unique_accommodation_channel');
            $table->index('connection_status');
            $table->index('last_sync_at');
            $table->index('sync_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartment_channels');
    }
};
