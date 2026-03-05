<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Booking;
use App\Models\Accommodation;
use App\Models\DistributionChannel;
use App\Models\Guest;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
     protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkIn = fake()->dateTimeBetween('now', '+3 months');
        $checkOut = (clone $checkIn)->modify('+' . fake()->numberBetween(1, 14) . ' days');
        $nights = (date_diff($checkIn, $checkOut))->days;
        
        $accommodation = Accommodation::inRandomOrder()->first() ?? Accommodation::factory();
        $basePrice = $accommodation instanceof Accommodation ? $accommodation->base_price : 100;
        $pricePerNight = $basePrice * fake()->randomFloat(2, 0.9, 1.1);
        $totalAmount = $pricePerNight * $nights;
        
        $status = fake()->randomElement(['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show']);
        $paymentStatus = 'pending';
        
        if ($status === 'checked_out') {
            $paymentStatus = fake()->randomElement(['paid', 'partial']);
        } elseif ($status === 'cancelled') {
            $paymentStatus = 'refunded';
        }
        
        return [
            'booking_reference' => 'BKG-' . strtoupper(Str::random(8)),
            'accommodation_id' => $accommodation instanceof Accommodation ? $accommodation->id : $accommodation->create()->id,
            'channel_id' => fake()->optional(0.6)->passThrough(DistributionChannel::inRandomOrder()->first()?->id),
            'guest_id' => Guest::factory(),
            'guest_temporal_id' => null,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'nights' => $nights,
            'adults' => fake()->numberBetween(1, 4),
            'children' => fake()->numberBetween(0, 2),
            'infants' => fake()->numberBetween(0, 1),
            'pets' => fake()->numberBetween(0, 1),
            'source' => fake()->randomElement(['direct', 'booking.com', 'airbnb', 'expedia']),
            'status' => $status,
            'price_per_night' => $pricePerNight,
            'base_price' => $pricePerNight * $nights,
            'cleaning_fee' => fake()->randomFloat(2, 0, 50),
            'service_fee' => fake()->randomFloat(2, 0, 30),
            'tax_amount' => fake()->randomFloat(2, 0, 50),
            'discount_amount' => fake()->randomFloat(2, 0, 100),
            'total_amount' => $totalAmount,
            'paid_amount' => $paymentStatus === 'paid' ? $totalAmount : ($paymentStatus === 'partial' ? $totalAmount / 2 : 0),
            'balance_due' => $totalAmount,
            'channel_commission_rate' => fake()->optional(0.5)->randomFloat(2, 10, 20),
            'channel_commission_amount' => fake()->optional(0.5)->randomFloat(2, 20, 200),
            'platform_fee' => fake()->optional(0.3)->randomFloat(2, 5, 30),
            'currency' => 'EUR',
            'payment_status' => $paymentStatus,
            'payment_due_date' => fake()->optional(0.3)->dateTimeBetween('now', '+1 month'),
            'guest_name' => fake()->name(),
            'guest_email' => fake()->optional(0.8)->safeEmail(),
            'guest_phone' => fake()->optional(0.6)->phoneNumber(),
            'guest_data' => null,
            'guest_notes' => fake()->optional(0.2)->sentence(),
            'staff_notes' => fake()->optional(0.1)->sentence(),
            'cancellation_reason' => $status === 'cancelled' ? fake()->sentence() : null,
            'confirmed_at' => in_array($status, ['confirmed', 'checked_in', 'checked_out']) ? fake()->dateTimeThisMonth() : null,
            'checked_in_at' => in_array($status, ['checked_in', 'checked_out']) ? fake()->dateTimeThisMonth() : null,
            'checked_out_at' => $status === 'checked_out' ? fake()->dateTimeThisMonth() : null,
            'cancelled_at' => $status === 'cancelled' ? fake()->dateTimeThisMonth() : null,
            'cancelled_by_user_id' => $status === 'cancelled' ? fake()->optional(0.5)->passThrough(User::inRandomOrder()->first()?->id) : null,
            'ip_address' => fake()->optional(0.3)->ipv4(),
            'user_agent' => fake()->optional(0.3)->userAgent(),
            'locale' => 'es',
        ];
    
    }
}
