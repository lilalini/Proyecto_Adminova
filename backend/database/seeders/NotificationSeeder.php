<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use App\Models\Owner;
use App\Models\Guest;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $owners = Owner::all();
        $guests = Guest::all();
        
        $notifiables = [
            ...$users->map(fn($u) => ['type' => User::class, 'id' => $u->id])->toArray(),
            ...$owners->map(fn($o) => ['type' => Owner::class, 'id' => $o->id])->toArray(),
            ...$guests->map(fn($g) => ['type' => Guest::class, 'id' => $g->id])->toArray(),
        ];

        foreach ($notifiables as $notifiable) {
            // 2-5 notificaciones por cada notifiable
            $numNotifications = rand(2, 5);
            
            for ($i = 0; $i < $numNotifications; $i++) {
                Notification::factory()->create([
                    'notifiable_type' => $notifiable['type'],
                    'notifiable_id' => $notifiable['id'],
                ]);
            }
        }
        
        // Notificación específica de prueba
        Notification::create([
            'notifiable_type' => User::class,
            'notifiable_id' => User::where('email', 'admin@example.com')->first()->id,
            'type' => 'welcome',
            'title' => '¡Bienvenido al sistema!',
            'body' => 'Gracias por registrarte. Comienza a gestionar tus propiedades.',
            'channels' => json_encode(['mail']),
            'is_read' => false,
            'sent_at' => now(),
        ]);
    
    }
}
