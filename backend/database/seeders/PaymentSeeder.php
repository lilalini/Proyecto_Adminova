<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Payment;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookings = Booking::all();

        foreach ($bookings as $booking) {
            $numPayments = 0;
            
            switch ($booking->payment_status) {
                case 'paid':
                    $numPayments = 1;
                    $status = 'completed';
                    break;
                case 'partial':
                    $numPayments = rand(1, 2);
                    $status = 'completed';
                    break;
                case 'pending':
                    $numPayments = fake()->boolean(30) ? 1 : 0;
                    $status = 'pending';
                    break;
                case 'refunded':
                    $numPayments = 1;
                    $status = 'refunded';
                    break;
                default:
                    $numPayments = 0;
            }
            
            if ($numPayments > 0) {
                for ($i = 0; $i < $numPayments; $i++) {
                    $amount = $i === 0 ? $booking->total_amount / $numPayments : $booking->total_amount / $numPayments;
                    
                    Payment::create([
                        'payment_reference' => 'PAY-' . $booking->booking_reference . '-' . ($i + 1),
                        'booking_id' => $booking->id,
                        'guest_id' => $booking->guest_id,
                        'payment_type' => $i === 0 ? 'deposit' : 'final',
                        'method' => fake()->randomElement(['credit_card', 'transfer', 'cash', 'paypal']),
                        'amount' => $amount,
                        'currency' => 'EUR',
                        'status' => $status,
                        'payment_date' => $booking->confirmed_at ?? now(),
                        'receipt_sent' => $status === 'completed',
                        'receipt_sent_at' => $status === 'completed' ? now() : null,
                    ]);
                }
            }
        }
    
    }
}
