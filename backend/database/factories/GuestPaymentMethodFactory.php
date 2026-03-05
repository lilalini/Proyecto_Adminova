<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\GuestPaymentMethod;
use App\Models\Guest;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GuestPaymentMethod>
 */
class GuestPaymentMethodFactory extends Factory
{
    protected $model = GuestPaymentMethod::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         $methodType = fake()->randomElement(['credit_card', 'paypal', 'apple_pay', 'google_pay']);
        $isCreditCard = $methodType === 'credit_card';
        
        $expiryMonth = fake()->numberBetween(1, 12);
        $currentYear = date('Y');
        $expiryYear = fake()->numberBetween($currentYear, $currentYear + 5);
        
        return [
            'guest_id' => Guest::factory(),
            'method_type' => $methodType,
            'token' => $isCreditCard ? fake()->uuid() : fake()->optional(0.8)->uuid(),
            'card_last_four' => $isCreditCard ? fake()->numerify('####') : null,
            'card_brand' => $isCreditCard ? fake()->randomElement(['visa', 'mastercard', 'amex']) : null,
            'card_expiry_month' => $isCreditCard ? str_pad($expiryMonth, 2, '0', STR_PAD_LEFT) : null,
            'card_expiry_year' => $isCreditCard ? (string)$expiryYear : null,
            'is_default' => false,
            'is_expired' => $isCreditCard ? ($expiryYear < $currentYear) : false,
            'last_used_at' => fake()->optional(0.3)->dateTimeThisYear(),
        ];
    }
    
}
