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
        Schema::create('cleaning_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_id')->constrained()->restrictOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete(); // tarea asociada a una reserva
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->enum('task_type', ['cleaning', 'maintenance', 'inspection', 'laundry'])->default('cleaning');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('checklist')->nullable();
            
            $table->datetime('scheduled_date');
            $table->datetime('completed_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            
            $table->json('photos')->nullable();
            $table->text('notes')->nullable();
            
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled', 'verified'])->default('pending');
            
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('status');
            $table->index('priority');
            $table->index('scheduled_date');
            $table->index('assigned_to_user_id');
            $table->index(['accommodation_id', 'status']); 
            $table->index(['assigned_to_user_id', 'status']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cleaning_tasks');
    }
};
