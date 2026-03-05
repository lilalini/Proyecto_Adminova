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
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
             // Datos personales (única vez)
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable(); // puede reservar sin email (ej: Airbnb)
            $table->string('phone')->nullable();
            
            // Datos legales (pueden cambiar con el tiempo)
            $table->string('document_type')->nullable();
            $table->string('document_number')->nullable();
            $table->string('nationality')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            
            // Metadata
            $table->string('source')->default('direct');
            $table->json('source_data')->nullable();
            $table->string('external_id')->nullable();
            
            $table->timestamps();
           
            
            // Evitar duplicados por email o documento
            $table->index('email');
            $table->index('document_number');
            $table->index('source');
        });

    }
        
        

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
