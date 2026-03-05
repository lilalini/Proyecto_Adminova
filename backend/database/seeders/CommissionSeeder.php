<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Commission;
use App\Models\DistributionChannel;
use App\Models\Owner;

class CommissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookings = Booking::where('status', 'checked_out')->get();
        $channels = DistributionChannel::all();
        
        foreach ($bookings as $booking) {
            $accommodation = $booking->accommodation;
            $owner = $accommodation->owner;
            
            // Comisión del canal (si aplica)
            if ($booking->channel_id && $booking->channel_commission_rate) {
                Commission::create([
                    'booking_id' => $booking->id,
                    'channel_id' => $booking->channel_id,
                    'accommodation_id' => $accommodation->id,
                    'owner_id' => $owner->id,
                    'commission_type' => 'channel',
                    'rate' => $booking->channel_commission_rate,
                    'amount' => $booking->channel_commission_amount ?? ($booking->total_amount * $booking->channel_commission_rate / 100),
                    'status' => 'calculated',
                    'currency' => 'EUR',
                ]);
            }
            
            // Comisión de la plataforma (ej: 5% fijo)
            Commission::create([
                'booking_id' => $booking->id,
                'accommodation_id' => $accommodation->id,
                'owner_id' => $owner->id,
                'commission_type' => 'platform',
                'rate' => 5.00,
                'amount' => $booking->total_amount * 0.05,
                'status' => 'calculated',
                'currency' => 'EUR',
            ]);
            
            // Comisión del owner (lo que gana él)
            Commission::create([
                'booking_id' => $booking->id,
                'accommodation_id' => $accommodation->id,
                'owner_id' => $owner->id,
                'commission_type' => 'owner',
                'rate' => $owner->commission_rate ?? 80,
                'amount' => $booking->total_amount * ($owner->commission_rate ?? 80) / 100,
                'status' => fake()->randomElement(['calculated', 'invoiced', 'paid']),
                'currency' => 'EUR',
            ]);
        }
        
        // Comisiones específicas para owner de prueba
        $testOwner = Owner::where('email', 'owner@example.com')->first();
        if ($testOwner) {
            $testBookings = Booking::whereHas('accommodation', fn($q) => $q->where('owner_id', $testOwner->id))->get();
            
            foreach ($testBookings as $booking) {
                Commission::where('booking_id', $booking->id)
                    ->where('commission_type', 'owner')
                    ->update(['status' => 'paid', 'paid_at' => now()]);
            }
        }
    
    }
}
