<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Accommodation;
use App\Models\Booking;
use App\Models\DistributionChannel;
use App\Models\Guest;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { $accommodations = Accommodation::where('status', 'published')->get();
        $channels = DistributionChannel::where('is_active', true)->get();
        
        foreach ($accommodations as $accommodation) {
            // 10-20 reservas por propiedad
            $numBookings = rand(5, 10);
            
            for ($i = 0; $i < $numBookings; $i++) {
                $guest = Guest::inRandomOrder()->first() ?? Guest::factory()->create();
                $channel = fake()->boolean(60) ? $channels->random() : null;
                
                $checkIn = Carbon::now()->subMonths(rand(1, 3))->addDays(rand(1, 30));
                $checkOut = (clone $checkIn)->addDays(rand(2, 7));
                $nights = $checkIn->diffInDays($checkOut);
                $totalAmount = $accommodation->base_price * $nights;
                
                Booking::create([
                    'booking_reference' => 'BKG-' . strtoupper(uniqid()),
                    'accommodation_id' => $accommodation->id,
                    'channel_id' => $channel?->id,
                    'guest_id' => $guest->id,
                    'guest_temporal_id' => null,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'nights' => $nights,
                    'adults' => rand(1, 4),
                    'children' => rand(0, 2),
                    'infants' => rand(0, 1),
                    'pets' => rand(0, 1),
                    'source' => $channel?->channel_code ?? 'direct',
                    'status' => fake()->randomElement(['confirmed', 'checked_out', 'cancelled']),
                    'price_per_night' => $accommodation->base_price,
                    'base_price' => $accommodation->base_price * $nights,
                    'cleaning_fee' => $accommodation->cleaning_fee ?? 0,
                    'service_fee' => 0,
                    'tax_amount' => 0,
                    'discount_amount' => 0,
                    'total_amount' => $totalAmount,
                    'paid_amount' => $totalAmount,
                    'balance_due' => 0,
                    'channel_commission_rate' => $channel?->commission_rate,
                    'channel_commission_amount' => $channel ? ($totalAmount * $channel->commission_rate / 100) : null,
                    'platform_fee' => null,
                    'currency' => 'EUR',
                    'payment_status' => 'paid',
                    'payment_due_date' => null,
                    'guest_name' => $guest->first_name . ' ' . $guest->last_name,
                    'guest_email' => $guest->email,
                    'guest_phone' => $guest->phone,
                    'guest_data' => null,
                    'guest_notes' => null,
                    'staff_notes' => null,
                    'cancellation_reason' => null,
                    'confirmed_at' => Carbon::now()->subDays(rand(1, 10)),
                    'checked_in_at' => $checkIn,
                    'checked_out_at' => $checkOut,
                    'cancelled_at' => null,
                    'cancelled_by_user_id' => null,
                    'ip_address' => null,
                    'user_agent' => null,
                    'locale' => 'es',
                ]);
            }
        }
        
        // Reserva específica de prueba
        $testAccommodation = Accommodation::where('title', 'Ático de Lujo Centro')->first();
        $testGuest = Guest::where('email', 'guest@example.com')->first();
        
        if ($testAccommodation && $testGuest) {
            $checkIn = Carbon::now()->addDays(7);
            $checkOut = Carbon::now()->addDays(14);
            $nights = 7;
            $totalAmount = $testAccommodation->base_price * $nights;
            
            Booking::create([
                'booking_reference' => 'BKG-TEST-001',
                'accommodation_id' => $testAccommodation->id,
                'channel_id' => null,
                'guest_id' => $testGuest->id,
                'guest_temporal_id' => null,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'nights' => $nights,
                'adults' => 2,
                'children' => 1,
                'infants' => 0,
                'pets' => 0,
                'source' => 'direct',
                'status' => 'confirmed',
                'price_per_night' => $testAccommodation->base_price,
                'base_price' => $totalAmount,
                'cleaning_fee' => $testAccommodation->cleaning_fee ?? 50,
                'service_fee' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'paid_amount' => $totalAmount,
                'balance_due' => 0,
                'channel_commission_rate' => null,
                'channel_commission_amount' => null,
                'platform_fee' => null,
                'currency' => 'EUR',
                'payment_status' => 'paid',
                'payment_due_date' => null,
                'guest_name' => $testGuest->first_name . ' ' . $testGuest->last_name,
                'guest_email' => $testGuest->email,
                'guest_phone' => $testGuest->phone,
                'guest_data' => null,
                'guest_notes' => null,
                'staff_notes' => null,
                'cancellation_reason' => null,
                'confirmed_at' => Carbon::now(),
                'checked_in_at' => null,
                'checked_out_at' => null,
                'cancelled_at' => null,
                'cancelled_by_user_id' => null,
                'ip_address' => null,
                'user_agent' => null,
                'locale' => 'es',
            ]);
        }
    
    }
}
