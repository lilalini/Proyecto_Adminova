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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_reference')->unique();
        
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // staff que registró
            
            $table->enum('payment_type', ['deposit', 'final', 'full', 'damage_deposit'])->default('full');
            $table->enum('method', ['credit_card', 'transfer', 'cash', 'paypal', 'stripe', 'other'])->default('cash');
            
            $table->string('transaction_id')->nullable(); // ID de pasarela
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('EUR');
            
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            
            $table->timestamp('payment_date')->nullable();
            $table->date('due_date')->nullable();
            
            $table->text('notes')->nullable();
            
            $table->boolean('receipt_sent')->default(false);
            $table->timestamp('receipt_sent_at')->nullable();
            
            $table->timestamp('refunded_at')->nullable();
            $table->text('refund_reason')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index('status');
            $table->index('payment_date');
            $table->index('due_date');
            $table->index('booking_id'); 
            $table->index(['booking_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
