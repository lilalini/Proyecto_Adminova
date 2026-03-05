<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Message;
use App\Models\User;
use App\Models\Owner;
use App\Models\Guest;
use App\Models\Accommodation;
use App\Models\Booking;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $senderTypes = [User::class, Owner::class, Guest::class];
        $receiverTypes = [User::class, Owner::class, Guest::class];
        
        $senderType = fake()->randomElement($senderTypes);
        $receiverType = fake()->randomElement($receiverTypes);
        
        $sender = $senderType::inRandomOrder()->first() ?? $senderType::factory();
        $receiver = $receiverType::inRandomOrder()->first() ?? $senderType::factory();
        
        $isRead = fake()->boolean(40);
        
        return [
            'sender_type' => $senderType,
            'sender_id' => $sender instanceof $senderType ? $sender->id : $sender->create()->id,
            'receiver_type' => $receiverType,
            'receiver_id' => $receiver instanceof $receiverType ? $receiver->id : $receiver->create()->id,
            'accommodation_id' => fake()->optional(0.5)->passThrough(Accommodation::inRandomOrder()->first()?->id),
            'booking_id' => fake()->optional(0.3)->passThrough(Booking::inRandomOrder()->first()?->id),
            'parent_id' => null,
            'subject' => fake()->sentence(6),
            'body' => fake()->paragraphs(3, true),
            'is_read' => $isRead,
            'read_at' => $isRead ? fake()->dateTimeThisMonth() : null,
            'message_type' => fake()->randomElement(['general', 'question', 'complaint', 'reservation']),
            'attachments' => fake()->optional(0.1)->json(),
            'priority' => fake()->randomElement(['low', 'normal', 'high']),
            'sent_at' => fake()->dateTimeThisMonth(),
        ];
    
    }
}
