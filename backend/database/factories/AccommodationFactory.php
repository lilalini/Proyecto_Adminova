<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Accommodation;
use App\Models\Owner;
use App\Models\CancellationPolicy;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Accommodation>
 */
class AccommodationFactory extends Factory
{
    protected $model = Accommodation::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       $title = fake()->unique()->streetName() . ' ' . fake()->randomElement(['Apartment', 'Loft', 'Studio', 'Duplex']);
        
        return [
            'owner_id' => Owner::factory(),
            'cancellation_policy_id' => CancellationPolicy::inRandomOrder()->first() ?? CancellationPolicy::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->paragraphs(3, true),
            'property_type' => fake()->randomElement(['apartment', 'house', 'studio', 'loft', 'villa']),
            'bedrooms' => fake()->numberBetween(1, 5),
            'bathrooms' => fake()->numberBetween(1, 4),
            'max_guests' => fake()->numberBetween(2, 10),
            'size_m2' => fake()->optional(0.8)->numberBetween(30, 200),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'country' => fake()->countryCode(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'base_price' => fake()->randomFloat(2, 50, 500),
            'weekly_discount' => fake()->randomFloat(2, 0, 20),
            'monthly_discount' => fake()->randomFloat(2, 0, 30),
            'cleaning_fee' => fake()->randomFloat(2, 0, 100),
            'security_deposit' => fake()->randomFloat(2, 0, 500),
            'minimum_stay' => fake()->numberBetween(1, 3),
            'maximum_stay' => fake()->optional(0.3)->numberBetween(14, 90),
            'amenities' => json_encode(fake()->randomElements(['wifi', 'tv', 'kitchen', 'ac', 'heating', 'pool', 'parking'], rand(3, 7))),
            'house_rules' => json_encode(['No parties', 'No smoking', 'Check-in after 15:00']),
            'check_in_time' => '15:00',
            'check_out_time' => '11:00',
            'status' => fake()->randomElement(['draft', 'published', 'maintenance', 'inactive']),
            'views' => fake()->numberBetween(0, 1000),
            'last_booking_at' => fake()->optional(0.3)->dateTimeThisYear(),
        ];
    
    }
}
