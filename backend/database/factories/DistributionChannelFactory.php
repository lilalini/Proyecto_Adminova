<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DistributionChannel;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DistributionChannel>
 */
class DistributionChannelFactory extends Factory
{
    protected $model = DistributionChannel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'channel_code' => fake()->unique()->randomElement(['booking', 'airbnb', 'expedia', 'vrbo', 'direct']),
            'name' => fake()->randomElement(['Booking.com', 'Airbnb', 'Expedia', 'VRBO', 'Direct Website']),
            'channel_type' => fake()->randomElement(['OTA', 'direct', 'corporate', 'referral']),
            'commission_rate' => fake()->randomFloat(2, 0, 25),
            'api_config' => fake()->optional(0.5)->json(),
            'is_active' => fake()->boolean(90),
            'sync_enabled' => fake()->boolean(80),
            'last_sync_at' => fake()->optional(0.3)->dateTimeThisMonth(),
        ];
    }
}
