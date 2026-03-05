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
        Schema::create('availability_calendars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Si se borra el user, que no se pierda el historial
            $table->date('date');
            $table->enum('status', ['available', 'booked', 'blocked', 'maintenance'])->default('available');
            $table->decimal('price', 10, 2)->nullable(); // null = usa precio base
            $table->integer('min_nights')->nullable();
            $table->integer('max_nights')->nullable();
            $table->boolean('closed_to_arrival')->default(false);
            $table->boolean('closed_to_departure')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Un accommodation no puede tener dos registros para la misma fecha
            $table->unique(['accommodation_id', 'date'], 'unique_accommodation_date');
            
            // Índices para búsquedas rápidas
            $table->index('date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availability_calendars');
    }
};
