<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Notification;
use App\Models\User;
use App\Models\Owner;
use App\Models\Guest;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Seleccionar notificable aleatorio (User, Owner o Guest)
        $notifiableType = fake()->randomElement([User::class, Owner::class, Guest::class]);
        $notifiable = $notifiableType::inRandomOrder()->first() ?? $notifiableType::factory();
        
        return [
            'notifiable_type' => $notifiableType,
            'notifiable_id' => $notifiable instanceof $notifiableType ? $notifiable->id : $notifiable->create()->id,
            'type' => fake()->randomElement(['booking_confirmation', 'payment_received', 'reminder', 'alert', 'promotion']),
            'title' => fake()->sentence(6),
            'body' => fake()->optional(0.8)->paragraph(),
            'data' => json_encode(['reference' => fake()->uuid(), 'url' => fake()->optional()->url()]),
            'channels' => json_encode(fake()->randomElements(['mail', 'sms', 'push'], rand(1, 3))),
            'is_read' => fake()->boolean(30),
            'read_at' => fake()->optional(0.3)->dateTimeThisMonth(),
            'sent_at' => fake()->dateTimeThisMonth(),
        ];
    
    }
}
