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
        Schema::create('owner_payout_methods', function (Blueprint $table) {
            $table->id();
        // Un owner no se puede borrar si tiene métodos de pago
            $table->foreignId('owner_id')->constrained()->restrictOnDelete();
            $table->string('method_type'); // bank_transfer, paypal, wise
            $table->string('account_holder');
            $table->string('account_number'); // IBAN o email
            $table->string('bank_name')->nullable();
            $table->string('bank_swift')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        
            $table->index(['owner_id', 'is_default']);// Búsquedas rápidas del método default
            $table->index('method_type');
            $table->index('is_verified');
        
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owner_payout_methods');
    }
};
