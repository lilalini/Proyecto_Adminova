<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SyncLog;
use App\Models\Accommodation;
use App\Models\DistributionChannel;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SyncLog>
 */
class SyncLogFactory extends Factory
{
    protected $model = SyncLog::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'processing', 'success', 'warning', 'error']);
        $startedAt = fake()->dateTimeThisMonth();
        $completedAt = in_array($status, ['success', 'warning', 'error']) 
            ? (clone $startedAt)->modify('+' . fake()->numberBetween(1, 60) . ' seconds')
            : null;
        
        $itemsTotal = fake()->numberBetween(1, 100);
        $itemsSuccess = $status === 'success' ? $itemsTotal : fake()->numberBetween(0, $itemsTotal);
        $itemsFailed = $itemsTotal - $itemsSuccess;
        
        return [
            'accommodation_id' => fake()->optional(0.7)->passThrough(Accommodation::inRandomOrder()->first()?->id),
            'channel_id' => fake()->optional(0.8)->passThrough(DistributionChannel::inRandomOrder()->first()?->id),
            'sync_type' => fake()->randomElement(['availability', 'prices', 'bookings', 'content']),
            'direction' => fake()->randomElement(['export', 'import', 'both']),
            'status' => $status,
            'started_at' => $startedAt,
            'completed_at' => $completedAt,
            'duration_seconds' => $completedAt 
                ? $completedAt->getTimestamp() - $startedAt->getTimestamp()
                : null,
            'items_total' => $itemsTotal,
            'items_success' => $itemsSuccess,
            'items_failed' => $itemsFailed,
            'request_data' => fake()->optional(0.5)->json(),
            'response_data' => fake()->optional(0.5)->json(),
            'error_message' => $status === 'error' ? fake()->sentence() : null,
            'error_trace' => $status === 'error' ? fake()->paragraph() : null,
            'retry_count' => $status === 'error' ? fake()->numberBetween(1, 3) : 0,
            'next_retry_at' => $status === 'error' ? fake()->optional(0.3)->dateTimeBetween('now', '+1 hour') : null,
            'created_by_user_id' => fake()->optional(0.3)->passThrough(User::inRandomOrder()->first()?->id),
        ];
    }
}
