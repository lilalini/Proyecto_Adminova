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
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_id')->constrained()->restrictOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete(); // donde se ganaron
            $table->integer('points');
            $table->enum('type', ['earned', 'redeemed', 'expired', 'adjusted'])->default('earned');
            $table->string('description')->nullable();
            $table->date('expiry_date')->nullable();
            
            // Si se canjearon
            $table->timestamp('redeemed_at')->nullable();
            $table->foreignId('redeemed_booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            
            // Ajustes manuales
            $table->foreignId('adjusted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['guest_id', 'type', 'expiry_date']);
            $table->index('expiry_date');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_points');
    }
};
