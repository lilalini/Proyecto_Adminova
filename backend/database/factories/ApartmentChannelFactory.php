<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Accommodation;
use App\Models\DistributionChannel;
use App\Models\ApartmentChannel;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApartmentChannel>
 */
class ApartmentChannelFactory extends Factory
{
    protected $model = ApartmentChannel::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         $adjustmentType = fake()->randomElement(['percentage', 'fixed', 'none']);
        
        return [
            'accommodation_id' => Accommodation::factory(),
            'distribution_channel_id' => DistributionChannel::factory(),
            'external_listing_id' => fake()->optional(0.7)->numerify('LIST-#####'),
            'external_url' => fake()->optional(0.5)->url(),
            'connection_status' => fake()->randomElement(['connected', 'disconnected', 'error']),
            'sync_enabled' => fake()->boolean(80),
            'sync_price' => fake()->boolean(90),
            'sync_availability' => fake()->boolean(90),
            'sync_content' => fake()->boolean(30),
            'price_adjustment_type' => $adjustmentType,
            'price_adjustment_value' => $adjustmentType !== 'none' ? fake()->randomFloat(2, 5, 30) : null,
            'min_stay_adjustment' => fake()->optional(0.2)->numberBetween(1, 7),
            'last_sync_at' => fake()->optional(0.4)->dateTimeThisMonth(),
            'last_sync_status' => fake()->optional(0.5)->randomElement(['success', 'error', 'pending']),
            'last_sync_message' => fake()->optional(0.2)->sentence(),
            'channel_data' => json_encode(['preferences' => fake()->words(3)]),
        ];
    
    }
}
