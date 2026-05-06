<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Commission;
use App\Models\Booking;
use App\Models\Accommodation;
use App\Models\Owner;
use App\Models\DistributionChannel;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Commission>
 */
class CommissionFactory extends Factory
{
    protected $model = Commission::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $booking = Booking::inRandomOrder()->first() ?? Booking::factory()->create();
        $accommodation = Accommodation::find($booking->accommodation_id) ?? Accommodation::factory()->create();
        $ownerId = $accommodation->owner_id ?? Owner::factory()->create()->id;

        $commissionType = fake()->randomElement(['channel', 'platform', 'owner']);
        $rate = fake()->randomFloat(2, 5, 25);
        $amount = ($booking->total_amount ?? fake()->randomFloat(2, 100, 1000)) * ($rate / 100);
        $status = fake()->randomElement(['pending', 'calculated', 'invoiced', 'paid']);

        return [
            'booking_id' => $booking->id,
            'channel_id' => $commissionType === 'channel' ? DistributionChannel::inRandomOrder()->value('id') : null,
            'accommodation_id' => $accommodation->id,
            'owner_id' => $ownerId,
            'commission_type' => $commissionType,
            'rate' => $rate,
            'amount' => $amount,
            'currency' => 'EUR',
            'status' => $status,
            'invoice_number' => in_array($status, ['invoiced', 'paid']) ? 'INV-' . fake()->numerify('######') : null,
            'invoice_date' => in_array($status, ['invoiced', 'paid']) ? fake()->dateTimeThisMonth() : null,
            'due_date' => $status === 'invoiced' ? fake()->dateTimeBetween('now', '+1 month') : null,
            'paid_at' => $status === 'paid' ? fake()->dateTimeThisMonth() : null,
            'notes' => fake()->optional(0.2)->sentence(),
        ];
    }
}
