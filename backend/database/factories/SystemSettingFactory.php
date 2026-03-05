<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SystemSetting;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SystemSetting>
 */
class SystemSettingFactory extends Factory
{
    protected $model = SystemSetting::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->word(),
            'value' => fake()->word(),
            'type' => fake()->randomElement(['string', 'boolean', 'integer', 'json']),
            'group' => fake()->randomElement(['general', 'booking', 'payment', 'email']),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
