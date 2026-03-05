<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Guest;
use Illuminate\Support\Facades\DB;

class BookingGuestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookings = Booking::all();

        foreach ($bookings as $booking) {
            // Guest principal (el que hizo la reserva)
            if ($booking->guest_id) {
                DB::table('booking_guest')->insert([
                    'booking_id' => $booking->id,
                    'guest_id' => $booking->guest_id,
                    'type' => 'main',
                    'first_name' => $booking->guest?->first_name,
                    'last_name' => $booking->guest?->last_name,
                    'legal_data_completed' => true,
                    'legal_data_completed_at' => $booking->confirmed_at ?? now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Acompañantes aleatorios (0-2)
            $numCompanions = rand(0, 2);
            $usedGuestIds = [$booking->guest_id]; // No repetir el guest principal

            for ($i = 0; $i < $numCompanions; $i++) {
                // Buscar un guest que no esté ya en esta booking
                $companion = Guest::whereNotIn('id', $usedGuestIds)->inRandomOrder()->first();
                
                if ($companion) {
                    $usedGuestIds[] = $companion->id;
                    
                    DB::table('booking_guest')->insert([
                        'booking_id' => $booking->id,
                        'guest_id' => $companion->id,
                        'type' => 'companion',
                        'first_name' => $companion->first_name,
                        'last_name' => $companion->last_name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}