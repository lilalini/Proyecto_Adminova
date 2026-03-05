<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $booking = Booking::inRandomOrder()->first() ?? Booking::factory();
        $status = fake()->randomElement(['pending', 'completed', 'failed', 'refunded']);
        $paymentDate = $status === 'completed' ? fake()->dateTimeThisMonth() : null;
        
        return [
            'payment_reference' => 'PAY-' . strtoupper(Str::random(10)),
            'booking_id' => $booking instanceof Booking ? $booking->id : $booking->create()->id,
            'guest_id' => fake()->optional(0.8)->passThrough(Guest::inRandomOrder()->first()?->id),
            'user_id' => fake()->optional(0.3)->passThrough(User::inRandomOrder()->first()?->id),
            'payment_type' => fake()->randomElement(['deposit', 'final', 'full', 'damage_deposit']),
            'method' => fake()->randomElement(['credit_card', 'transfer', 'cash', 'paypal', 'stripe', 'other']),
            'transaction_id' => fake()->optional(0.7)->uuid(),
            'amount' => fake()->randomFloat(2, 50, 2000),
            'currency' => 'EUR',
            'status' => $status,
            'payment_date' => $paymentDate,
            'due_date' => fake()->optional(0.3)->dateTimeBetween('now', '+1 month'),
            'notes' => fake()->optional(0.2)->sentence(),
            'receipt_sent' => $status === 'completed' ? fake()->boolean(70) : false,
            'receipt_sent_at' => $status === 'completed' && fake()->boolean(70) ? $paymentDate : null,
            'refunded_at' => $status === 'refunded' ? fake()->dateTimeThisMonth() : null,
            'refund_reason' => $status === 'refunded' ? fake()->sentence() : null,
        ];
    
    }
}
