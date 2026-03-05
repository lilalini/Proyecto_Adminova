<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\OwnerPayoutMethod;
use App\Models\Owner;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OwnerPayoutMethod>
 */
class OwnerPayoutMethodFactory extends Factory
{
    protected $model = OwnerPayoutMethod::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $methodType = fake()->randomElement(['bank_transfer', 'paypal', 'wise']);
        return [
            'owner_id' => Owner::factory(),
            'method_type' => $methodType,
            'account_holder' => fake()->name(),
            'account_number' => $methodType === 'paypal' 
                ? fake()->safeEmail() 
                : fake()->iban('ES'),
            'bank_name' => fake()->optional(0.7)->company(),
            'bank_swift' => fake()->optional(0.5)->swiftBicNumber(),
            'is_default' => false,
            'is_verified' => fake()->boolean(30),
            'verified_at' => fake()->optional(0.3)->dateTimeThisYear(),
            'notes' => fake()->optional(0.2)->sentence(),
        ];
    }
}
