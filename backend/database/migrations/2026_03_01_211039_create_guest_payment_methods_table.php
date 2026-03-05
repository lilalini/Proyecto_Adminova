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
            Schema::create('guest_payment_methods', function (Blueprint $table) {
                $table->id();
            // Un guest no se puede borrar si tiene métodos de pago
                $table->foreignId('guest_id')->constrained()->restrictOnDelete();
                $table->string('method_type'); // credit_card, paypal, etc.
                $table->string('token')->nullable(); // token de la pasarela
                $table->string('card_last_four', 4)->nullable();
                $table->string('card_brand')->nullable(); // visa, mastercard
                $table->string('card_expiry_month', 2)->nullable();
                $table->string('card_expiry_year', 4)->nullable();
                $table->boolean('is_default')->default(false);
                $table->boolean('is_expired')->default(false);
                $table->timestamp('last_used_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                // Indices para optimizar consultas comunes
                $table->index(['guest_id', 'is_default']);
                $table->index('method_type');
                $table->index('is_expired');
                $table->index('last_used_at');
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('guest_payment_methods');
        }
    };
