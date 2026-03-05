<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\LoyaltySetting;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoyaltySetting>
 */
class LoyaltySettingFactory extends Factory
{
    protected $model = LoyaltySetting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Bronze', 'Silver', 'Gold', 'Platinum']),
            'points_per_currency' => fake()->randomFloat(2, 1, 20),
            'points_to_currency_ratio' => fake()->randomFloat(2, 0.01, 0.10),
            'min_redemption' => fake()->numberBetween(50, 500),
            'expiry_days' => fake()->numberBetween(180, 730),
            'max_discount' => fake()->randomFloat(2, 5, 50),
            'is_active' => fake()->boolean(80),
            'valid_from' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'valid_until' => fake()->optional()->dateTimeBetween('now', '+2 years'),
        ];
    }
}
