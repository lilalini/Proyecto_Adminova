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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            // Remitente (polimórfico)
            $table->string('sender_type');
            $table->unsignedBigInteger('sender_id');
            
            // Destinatario (polimórfico)
            $table->string('receiver_type');
            $table->unsignedBigInteger('receiver_id');
            
            // Contexto
            $table->foreignId('accommodation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('messages')->nullOnDelete();
            
            $table->string('subject');
            $table->text('body');
            
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            
            $table->enum('message_type', ['general', 'question', 'complaint', 'reservation'])->default('general');
            $table->json('attachments')->nullable();
            $table->enum('priority', ['low', 'normal', 'high'])->default('normal');
            
            $table->timestamp('sent_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['sender_type', 'sender_id']);
            $table->index(['receiver_type', 'receiver_id']);
            $table->index('is_read');
            $table->index('sent_at');
            $table->index(['booking_id', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
