<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\LoyaltyPoint;
use App\Models\Guest;
use App\Models\Booking;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoyaltyPoint>
 */
class LoyaltyPointFactory extends Factory
{
     protected $model = LoyaltyPoint::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       $type = fake()->randomElement(['earned', 'redeemed', 'expired', 'adjusted']);
        $expiryDate = $type === 'earned' ? fake()->dateTimeBetween('+6 months', '+2 years') : null;
        
        return [
            'guest_id' => Guest::factory(),
            'booking_id' => $type === 'earned' ? Booking::factory() : null,
            'points' => $type === 'earned' ? fake()->numberBetween(50, 500) : fake()->numberBetween(-500, -50),
            'type' => $type,
            'description' => fake()->sentence(),
            'expiry_date' => $expiryDate,
            'redeemed_at' => $type === 'redeemed' ? fake()->dateTimeThisMonth() : null,
            'redeemed_booking_id' => $type === 'redeemed' ? Booking::factory() : null,
            'adjusted_by_user_id' => $type === 'adjusted' ? User::factory() : null,
        ];
    
    }
}
