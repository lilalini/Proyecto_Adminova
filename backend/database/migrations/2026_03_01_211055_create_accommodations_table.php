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
        Schema::create('accommodations', function (Blueprint $table) {
            $table->id();
        // Un owner no se puede borrar si tiene propiedades
            $table->foreignId('owner_id')->constrained()->restrictOnDelete();

            $table->foreignId('cancellation_policy_id')->constrained()->restrictOnDelete();  // No podemos borrar una política si hay alojamientos usándola (restrict)
            
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            
            $table->string('property_type');
            $table->integer('bedrooms')->default(1);
            $table->integer('bathrooms')->default(1);
            $table->integer('max_guests')->default(2);
            $table->integer('size_m2')->nullable();
            
            $table->string('address');
            $table->string('city');
            $table->string('postal_code');
            $table->string('country')->default('ES');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            
            $table->decimal('base_price', 10, 2);
            $table->decimal('weekly_discount', 5, 2)->default(0);
            $table->decimal('monthly_discount', 5, 2)->default(0);
            $table->decimal('cleaning_fee', 10, 2)->default(0);
            $table->decimal('security_deposit', 10, 2)->default(0);
            
            $table->integer('minimum_stay')->default(1);
            $table->integer('maximum_stay')->nullable();
            
            $table->json('amenities')->nullable();
            $table->json('house_rules')->nullable();
            
            $table->string('check_in_time')->default('15:00');
            $table->string('check_out_time')->default('11:00');
            
            $table->enum('status', ['draft', 'published', 'maintenance', 'inactive'])->default('draft');
            $table->integer('views')->default(0);
            $table->timestamp('last_booking_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('city');
            $table->index('status');
            $table->index('owner_id');
            $table->index('cancellation_policy_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accommodations');
    }
};
