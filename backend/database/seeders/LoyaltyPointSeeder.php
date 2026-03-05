<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Guest;
use App\Models\Booking;
use App\Models\LoyaltyPoint;
use App\Models\User;

class LoyaltyPointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guests = Guest::all();
        $admin = User::where('email', 'admin@example.com')->first();
        
        foreach ($guests as $guest) {
            // Cada guest gana puntos por sus reservas
            $bookings = Booking::where('guest_id', $guest->id)->where('status', 'checked_out')->get();
            
            foreach ($bookings as $booking) {
                // 10 puntos por cada 100€ (ejemplo)
                $points = round($booking->total_amount / 10);
                
                LoyaltyPoint::create([
                    'guest_id' => $guest->id,
                    'booking_id' => $booking->id,
                    'points' => $points,
                    'type' => 'earned',
                    'description' => 'Puntos por reserva ' . $booking->booking_reference,
                    'expiry_date' => now()->addYear(),
                ]);
            }
            
            // Algunos guests canjean puntos
            if (fake()->boolean(30) && $bookings->count() > 0) {
                $earnedPoints = LoyaltyPoint::where('guest_id', $guest->id)->where('type', 'earned')->sum('points');
                $redeemPoints = min(200, $earnedPoints);
                
                LoyaltyPoint::create([
                    'guest_id' => $guest->id,
                    'points' => -$redeemPoints,
                    'type' => 'redeemed',
                    'description' => 'Canje por descuento',
                    'redeemed_at' => now(),
                    'redeemed_booking_id' => $bookings->random()->id,
                ]);
            }
            
            // Guest VIP específico
            if ($guest->email === 'guest@example.com') {
                LoyaltyPoint::create([
                    'guest_id' => $guest->id,
                    'booking_id' => Booking::where('guest_id', $guest->id)->first()?->id,
                    'points' => 1000,
                    'type' => 'earned',
                    'description' => 'Bienvenida VIP',
                    'expiry_date' => now()->addYears(2),
                ]);
                
                LoyaltyPoint::create([
                    'guest_id' => $guest->id,
                    'points' => 500,
                    'type' => 'earned',
                    'description' => 'Ajuste manual por incidencia',
                    'adjusted_by_user_id' => $admin->id,
                ]);
            }
        }
    }
}
