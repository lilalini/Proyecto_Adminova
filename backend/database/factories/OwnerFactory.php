<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Owner;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Owner>
 */
class OwnerFactory extends Factory
{

    protected $model = Owner::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
             'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'phone' => fake()->phoneNumber(),
            'document_type' => fake()->randomElement(['DNI', 'NIE', 'Passport']),
            'document_number' => fake()->unique()->bothify('########?'),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'country' => fake()->country(),
            'iban' => fake()->optional(0.7)->iban('ES'),
            'contract_signed' => fake()->boolean(80),
            'contract_date' => fake()->optional(0.8)->dateTimeBetween('-2 years', 'now'),
            'is_active' => fake()->boolean(90),
            'email_verified_at' => fake()->optional(0.8)->dateTimeThisYear(),
            'last_login_at' => fake()->optional(0.5)->dateTimeThisMonth(),
            'last_login_ip' => fake()->optional(0.5)->ipv4(),
            'remember_token' => Str::random(10),
        ];
    }
}