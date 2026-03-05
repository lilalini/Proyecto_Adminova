<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Accommodation;
use App\Models\Booking;
use App\Models\User;
use App\Models\CleaningTask;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CleaningTask>
 */
class CleaningTaskFactory extends Factory
{
    protected $model = CleaningTask::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'in_progress', 'completed', 'cancelled', 'verified']);
        $completedAt = in_array($status, ['completed', 'verified']) ? fake()->dateTimeThisMonth() : null;
        $verifiedAt = $status === 'verified' ? fake()->dateTimeThisMonth() : null;
        
        return [
            'accommodation_id' => Accommodation::factory(),
            'booking_id' => fake()->optional(0.6)->passThrough(Booking::inRandomOrder()->first()?->id),
            'assigned_to_user_id' => fake()->optional(0.7)->passThrough(User::inRandomOrder()->first()?->id),
            'created_by_user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'task_type' => fake()->randomElement(['cleaning', 'maintenance', 'inspection', 'laundry']),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'title' => fake()->sentence(4),
            'description' => fake()->optional(0.7)->paragraph(),
            'checklist' => json_encode(fake()->randomElements(['cambiar sábanas', 'limpiar baño', 'aspirar', 'reponer amenities'], rand(2, 4))),
            'scheduled_date' => fake()->dateTimeBetween('now', '+1 month'),
            'completed_at' => $completedAt,
            'duration_minutes' => $completedAt ? fake()->numberBetween(30, 180) : null,
            'photos' => fake()->optional(0.3)->json(),
            'notes' => fake()->optional(0.4)->sentence(),
            'status' => $status,
            'verified_by_user_id' => $verifiedAt ? User::inRandomOrder()->first()?->id : null,
            'verified_at' => $verifiedAt,
        ];
    
    }
}
