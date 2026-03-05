<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CancellationPolicy;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CancellationPolicy>
 */
class CancellationPolicyFactory extends Factory
{
    protected $model = CancellationPolicy::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Flexible', 
                'Moderada', 
                'Estricta', 
                'No reembolsable'
            ]),
            'description' => fake()->sentence(),
            'free_cancellation_days' => fake()->numberBetween(0, 30),
            'penalty_percentage' => fake()->randomFloat(2, 0, 100),
            'is_default' => false,
            'is_active' => true,
        ];
    }
        
}
