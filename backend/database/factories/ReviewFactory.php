<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Review;
use App\Models\Booking;
use App\Models\Accommodation;
use App\Models\Guest;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $booking = Booking::inRandomOrder()->first() ?? Booking::factory();
        $rating = fake()->numberBetween(1, 5);
        $status = fake()->randomElement(['pending', 'published', 'rejected', 'archived']);
        
        return [
            'accommodation_id' => $booking->accommodation_id ?? Accommodation::factory(),
            'booking_id' => $booking->id,
            'guest_id' => $booking->guest_id ?? Guest::factory(),
            'user_id' => fake()->optional(0.4)->passThrough(User::inRandomOrder()->first()?->id),
            'rating' => $rating,
            'cleanliness_rating' => fake()->optional(0.7)->numberBetween(1, 5),
            'communication_rating' => fake()->optional(0.7)->numberBetween(1, 5),
            'location_rating' => fake()->optional(0.7)->numberBetween(1, 5),
            'value_rating' => fake()->optional(0.7)->numberBetween(1, 5),
            'title' => fake()->optional(0.6)->sentence(6),
            'comment' => fake()->paragraphs(2, true),
            'host_response' => $status === 'published' && fake()->boolean(50) ? fake()->paragraph() : null,
            'host_responded_at' => $status === 'published' && fake()->boolean(50) ? fake()->dateTimeThisMonth() : null,
            'status' => $status,
            'source' => fake()->randomElement(['direct', 'booking.com', 'airbnb', 'expedia']),
            'external_review_id' => fake()->optional(0.3)->uuid(),
            'is_verified' => fake()->boolean(70),
            'helpful_votes' => fake()->numberBetween(0, 20),
            'published_at' => $status === 'published' ? fake()->dateTimeThisMonth() : null,
        ];
    
    }
}
