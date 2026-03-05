<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Owner;
use App\Models\Guest;
use App\Models\Booking;
use App\Models\Message;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $owners = Owner::all();
        $guests = Guest::all();
        $bookings = Booking::where('status', 'confirmed')->get();
        
        // Conversaciones entre guests y owners
        foreach ($bookings->take(20) as $booking) {
            $guest = $booking->guest;
            $accommodation = $booking->accommodation;
            $owner = $accommodation->owner;
            
            // Mensaje del guest al owner
            $guestMsg = Message::create([
                'sender_type' => Guest::class,
                'sender_id' => $guest->id,
                'receiver_type' => Owner::class,
                'receiver_id' => $owner->id,
                'accommodation_id' => $accommodation->id,
                'booking_id' => $booking->id,
                'subject' => 'Pregunta sobre mi reserva',
                'body' => 'Hola, quería confirmar el horario de check-in. ¿Podemos llegar a las 14:00?',
                'message_type' => 'question',
                'priority' => 'normal',
                'sent_at' => now()->subDays(rand(1, 10)),
                'is_read' => fake()->boolean(70),
            ]);
            
            // Respuesta del owner (si aplica)
            if (fake()->boolean(80)) {
                Message::create([
                    'sender_type' => Owner::class,
                    'sender_id' => $owner->id,
                    'receiver_type' => Guest::class,
                    'receiver_id' => $guest->id,
                    'accommodation_id' => $accommodation->id,
                    'booking_id' => $booking->id,
                    'parent_id' => $guestMsg->id,
                    'subject' => 'Re: ' . $guestMsg->subject,
                    'body' => '¡Hola! Por supuesto, a las 14:00 está perfecto. Te esperamos.',
                    'message_type' => 'general',
                    'priority' => 'normal',
                    'sent_at' => now()->subDays(rand(1, 9)),
                    'is_read' => true,
                ]);
            }
        }
        
        // Mensajes del admin a owners
        foreach ($owners->take(5) as $owner) {
            Message::create([
                'sender_type' => User::class,
                'sender_id' => $users->first()->id,
                'receiver_type' => Owner::class,
                'receiver_id' => $owner->id,
                'subject' => 'Recordatorio: Documentación pendiente',
                'body' => 'Estimado propietario, le recordamos que debe actualizar su documentación fiscal.',
                'message_type' => 'general',
                'priority' => 'normal',
                'sent_at' => now()->subDays(rand(1, 5)),
                'is_read' => fake()->boolean(50),
            ]);
        }
        
        // Mensaje específico para guest de prueba
        $testGuest = Guest::where('email', 'guest@example.com')->first();
        if ($testGuest) {
            Message::create([
                'sender_type' => User::class,
                'sender_id' => $users->first()->id,
                'receiver_type' => Guest::class,
                'receiver_id' => $testGuest->id,
                'subject' => '¡Bienvenido a nuestro sistema!',
                'body' => 'Gracias por confiar en nosotros. Si necesita cualquier cosa, no dude en contactarnos.',
                'message_type' => 'general',
                'priority' => 'low',
                'sent_at' => now(),
                'is_read' => false,
            ]);
        }
    }
}
