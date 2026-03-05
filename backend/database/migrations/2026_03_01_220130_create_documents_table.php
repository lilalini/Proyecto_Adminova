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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            // Relación polimórfica
            $table->string('documentable_type');
            $table->unsignedBigInteger('documentable_id');
            
            $table->string('document_type'); // contract, id_card, passport, etc.
            $table->string('title');
            $table->string('file_name');
            $table->string('file_path');
            $table->bigInteger('file_size')->nullable(); // en bytes
            $table->string('mime_type')->nullable();
            
            $table->boolean('is_signed')->default(false);
            $table->timestamp('signed_at')->nullable();
            
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // datos específicos del documento
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['documentable_type', 'documentable_id']);
            $table->index('document_type');
            $table->index('valid_until');
            $table->index('is_verified');
            $table->index(['valid_until', 'is_verified']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
