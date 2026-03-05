<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Guest;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Guest>
 */
class GuestFactory extends Factory
{
     protected $model = Guest::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hasDocument = fake()->boolean(60);
        $hasEmail = fake()->boolean(80);
        
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => $hasEmail ? fake()->unique()->safeEmail() : null,
            'phone' => fake()->optional(0.7)->phoneNumber(),
            'document_type' => $hasDocument ? fake()->randomElement(['DNI', 'NIE', 'Passport']) : null,
            'document_number' => $hasDocument ? fake()->unique()->bothify('########?') : null,
            'nationality' => fake()->optional(0.7)->countryCode(),
            'birth_date' => fake()->optional(0.6)->dateTimeBetween('-70 years', '-18 years'),
            'gender' => fake()->optional(0.5)->randomElement(['male', 'female', 'other']),
            'address' => fake()->optional(0.6)->streetAddress(),
            'city' => fake()->optional(0.6)->city(),
            'postal_code' => fake()->optional(0.6)->postcode(),
            'country' => fake()->optional(0.6)->country(),
            'source' => fake()->randomElement(['direct', 'booking.com', 'airbnb', 'expedia', 'phone']),
            'source_data' => fake()->optional(0.3)->passthrough(json_encode(['source' => fake()->word()])),
            'external_id' => fake()->optional(0.3)->uuid(),
        ];
    }
        
}
