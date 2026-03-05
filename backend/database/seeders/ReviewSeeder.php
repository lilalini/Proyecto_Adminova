<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use App\Models\Accommodation;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookings = Booking::where('status', 'checked_out')->get();
        $admin = User::where('email', 'admin@example.com')->first();
        
        foreach ($bookings as $booking) {
            // 70% de las reservas completadas tienen review
            if (fake()->boolean(70)) {
                $rating = fake()->numberBetween(3, 5); // sesgo a positivo
                
                $review = Review::create([
                    'accommodation_id' => $booking->accommodation_id,
                    'booking_id' => $booking->id,
                    'guest_id' => $booking->guest_id,
                    'user_id' => null,
                    'rating' => $rating,
                    'cleanliness_rating' => fake()->numberBetween($rating-1, 5),
                    'communication_rating' => fake()->numberBetween($rating-1, 5),
                    'location_rating' => fake()->numberBetween($rating-1, 5),
                    'value_rating' => fake()->numberBetween($rating-1, 5),
                    'title' => $rating >= 4 ? '¡Excelente estancia!' : 'Buena estancia',
                    'comment' => fake()->paragraph(),
                    'status' => 'published',
                    'source' => $booking->source,
                    'is_verified' => true,
                    'published_at' => $booking->checked_out_at ?? now(),
                ]);
                
                // 50% de las reviews publicadas tienen respuesta del host
                if (fake()->boolean(50)) {
                    $review->update([
                        'host_response' => '¡Gracias por tu comentario! Nos alegramos de que hayas disfrutado.',
                        'host_responded_at' => now(),
                        'user_id' => $admin?->id,
                    ]);
                }
            }
        }
        
      // Reviews específicas para propiedad de prueba
        $testAccommodation = Accommodation::where('title', 'Ático de Lujo Centro')->first();
        if ($testAccommodation) {
            $testBookings = Booking::where('accommodation_id', $testAccommodation->id)
                ->whereDoesntHave('review')
                ->take(5)
                ->get();
            
            foreach ($testBookings as $booking) {
                Review::factory()->create([
                    'accommodation_id' => $testAccommodation->id,
                    'booking_id' => $booking->id,
                    'guest_id' => $booking->guest_id,
                    'status' => 'published',
                    'rating' => 5,
                ]);
            }
        }
    }
}
