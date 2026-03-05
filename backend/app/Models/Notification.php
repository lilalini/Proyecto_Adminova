<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    /** @use HasFactory<\Database\Factories\NotificationFactory> */
    use HasFactory, SoftDeletes;

     protected $fillable = [
        'notifiable_type', // User, Owner, Guest
        'notifiable_id',
        'type', // booking_confirmed, payment_received, check_in_reminder, etc.
        'title',
        'body',
        'data', // JSON con datos adicionales
        'channels', // ['mail', 'sms', 'push', 'database']
        'is_read',
        'read_at',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'channels' => 'array',
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    // Relación polimórfica
    public function notifiable()
    {
        return $this->morphTo();
    }

    // Scope para no leídas
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    // Scope para tipo específico
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Marcar como leída
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }
}
