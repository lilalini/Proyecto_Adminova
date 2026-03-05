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
        Schema::create('loyalty_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('points_per_currency', 8, 2); // ej: 10 puntos por euro
            $table->decimal('points_to_currency_ratio', 8, 2); // ej: 0.01 = 1 punto = 1 céntimo
            $table->integer('min_redemption')->default(100);
            $table->integer('expiry_days')->default(365);
            $table->decimal('max_discount', 5, 2)->default(20.00); // % máximo
            $table->boolean('is_active')->default(true);
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
            $table->index(['valid_from', 'valid_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_settings');
    }
};
