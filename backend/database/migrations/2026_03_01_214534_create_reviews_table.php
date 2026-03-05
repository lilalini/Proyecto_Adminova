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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
        // Las reviews son histórico, no se borran aunque el accommodation se desactive
            $table->foreignId('accommodation_id')->constrained()->restrictOnDelete();
            $table->foreignId('booking_id')->constrained()->nullOnDelete();
        // Guest no se borra, sus reviews quedan
            $table->foreignId('guest_id')->constrained()->restrictOnDelete();
        // Staff puede irse, la respuesta queda
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); 
            
            $table->integer('rating'); // 1-5
            $table->integer('cleanliness_rating')->nullable();
            $table->integer('communication_rating')->nullable();
            $table->integer('location_rating')->nullable();
            $table->integer('value_rating')->nullable();
            
            $table->string('title')->nullable();
            $table->text('comment');
            
            $table->text('host_response')->nullable();
            $table->timestamp('host_responded_at')->nullable();
            
            $table->enum('status', ['pending', 'published', 'rejected', 'archived'])->default('pending');
            $table->string('source')->default('direct');
            $table->string('external_review_id')->nullable();
            
            $table->boolean('is_verified')->default(false);
            $table->integer('helpful_votes')->default(0);
            
            $table->timestamp('published_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Una reserva solo puede tener una review
            $table->unique('booking_id');
            
            // Índices
            $table->index('status');
            $table->index('rating');
            $table->index('published_at');
            $table->index(['accommodation_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
