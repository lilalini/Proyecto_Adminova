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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique();
            
            // Relaciones
            $table->foreignId('accommodation_id')->constrained()->restrictOnDelete();
            $table->foreignId('channel_id')->nullable()->constrained('distribution_channels')->nullOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('guest_temporal_id')->nullable(); // si usamos
            
            // Fechas
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('nights');
            
            // Ocupación
            $table->integer('adults')->default(1);
            $table->integer('children')->default(0);
            $table->integer('infants')->default(0);
            $table->integer('pets')->default(0);
            
            // Origen y estado
            $table->string('source')->default('direct');
            $table->enum('status', ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show'])->default('pending');
            
            // Precios
            $table->decimal('price_per_night', 10, 2);
            $table->decimal('base_price', 10, 2);
            $table->decimal('cleaning_fee', 10, 2)->default(0);
            $table->decimal('service_fee', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('balance_due', 10, 2);
            
            // Comisiones
            $table->decimal('channel_commission_rate', 5, 2)->nullable();
            $table->decimal('channel_commission_amount', 10, 2)->nullable();
            $table->decimal('platform_fee', 10, 2)->nullable();
            
            // Pago
            $table->string('currency')->default('EUR');
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'refunded'])->default('pending');
            $table->date('payment_due_date')->nullable();
            
            // Datos del cliente (snapshot)
            $table->string('guest_name');
            $table->string('guest_email')->nullable();
            $table->string('guest_phone')->nullable();
            $table->json('guest_data')->nullable();
            
            // Notas
            $table->text('guest_notes')->nullable();
            $table->text('staff_notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            
            // Fechas clave
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Metadatos
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('locale')->default('es');
            
            $table->timestamps();
          
            
            // Índices
            $table->index('check_in');
            $table->index('check_out');
            $table->index('status');
            $table->index('guest_email');
            $table->index('booking_reference');
            $table->index('payment_status');
            $table->index(['guest_id', 'status']); 
            $table->index(['accommodation_id', 'check_in']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
