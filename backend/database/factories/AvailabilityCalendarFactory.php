<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Accommodation;
use App\Models\User;
use App\Models\AvailabilityCalendar;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AvailabilityCalendar>
 */
class AvailabilityCalendarFactory extends Factory
{
    protected $model = AvailabilityCalendar::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         $status = fake()->randomElement(['available', 'booked', 'blocked', 'maintenance']);
        
        return [
            'accommodation_id' => Accommodation::factory(),
            'user_id' => fake()->optional(0.3)->passThrough(User::inRandomOrder()->first()?->id ?? User::factory()),
            'date' => fake()->dateTimeBetween('now', '+1 year'),
            'status' => $status,
            'price' => $status === 'blocked' ? null : fake()->optional(0.3)->randomFloat(2, 50, 500),
            'min_nights' => fake()->optional(0.2)->numberBetween(1, 7),
            'max_nights' => fake()->optional(0.1)->numberBetween(14, 30),
            'closed_to_arrival' => fake()->boolean(10),
            'closed_to_departure' => fake()->boolean(10),
            'notes' => fake()->optional(0.1)->sentence(),
        ];
    
    }
}
