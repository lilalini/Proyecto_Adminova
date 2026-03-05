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
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('channel_id')->nullable()->constrained('distribution_channels')->nullOnDelete();
            
            $table->enum('sync_type', ['availability', 'prices', 'bookings', 'content'])->default('availability');
            $table->enum('direction', ['export', 'import', 'both'])->default('export');
            
            $table->enum('status', ['pending', 'processing', 'success', 'warning', 'error'])->default('pending');
            
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            
            $table->integer('items_total')->default(0);
            $table->integer('items_success')->default(0);
            $table->integer('items_failed')->default(0);
            
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            
            $table->text('error_message')->nullable();
            $table->text('error_trace')->nullable();
            
            $table->integer('retry_count')->default(0);
            $table->timestamp('next_retry_at')->nullable();
            
            $table->foreignId('created_by_user_id')->nullable()->constrained('users');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('status');
            $table->index('sync_type');
            $table->index('created_at');
            $table->index(['channel_id', 'status', 'created_at']);
            $table->index(['accommodation_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};
