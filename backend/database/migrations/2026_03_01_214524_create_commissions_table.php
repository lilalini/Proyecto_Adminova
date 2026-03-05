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
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('channel_id')->nullable()->constrained('distribution_channels')->nullOnDelete();
            $table->foreignId('accommodation_id')->constrained()->restrictOnDelete();
            $table->foreignId('owner_id')->constrained()->restrictOnDelete();
            
            $table->enum('commission_type', ['channel', 'platform', 'owner'])->default('channel');
            $table->decimal('rate', 5, 2); // porcentaje
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('EUR');
            
            $table->enum('status', ['pending', 'calculated', 'invoiced', 'paid'])->default('pending');
            
            $table->string('invoice_number')->nullable();
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            $table->text('notes')->nullable();
            
            $table->timestamps();
        
            
            // Índices
            $table->index('status');
            $table->index('owner_id');
            $table->index('invoice_number');
            $table->index(['owner_id', 'status']); 
            $table->index('due_date'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
