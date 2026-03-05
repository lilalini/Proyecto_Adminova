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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            // Relación polimórfica
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            
            $table->string('collection_name')->default('default');
            $table->string('name');
            $table->string('file_name');
            $table->string('file_path');
            $table->bigInteger('file_size');
            $table->string('mime_type');
            $table->string('disk')->default('public');
            
            $table->integer('order')->default(0);
            $table->boolean('is_main')->default(false);
            
            $table->string('alt_text')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            
            $table->json('metadata')->nullable(); // dimensiones, GPS, etc.
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['model_type', 'model_id']);
            $table->index('collection_name');
            $table->index('is_main');
            $table->index(['model_type', 'model_id', 'collection_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
