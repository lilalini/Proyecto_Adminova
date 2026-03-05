<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    /** @use HasFactory<\Database\Factories\MessageFactory> */
    use HasFactory, SoftDeletes;

     protected $fillable = [
        'sender_type', // User, Owner, Guest
        'sender_id',
        'receiver_type', // User, Owner, Guest
        'receiver_id',
        'accommodation_id',
        'booking_id',
        'parent_id', // para hilos de conversación
        'subject',
        'body',
        'is_read',
        'read_at',
        'message_type', // general, question, complaint, reservation
        'attachments', // JSON con archivos adjuntos
        'priority', // low, normal, high
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    // Relaciones polimórficas
    public function sender()
    {
        return $this->morphTo();
    }

    public function receiver()
    {
        return $this->morphTo();
    }

    // Relaciones normales
    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function parent()
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'parent_id');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForReceiver($query, $receiverType, $receiverId)
    {
        return $query->where('receiver_type', $receiverType)
                     ->where('receiver_id', $receiverId);
    }

    public function scopeForSender($query, $senderType, $senderId)
    {
        return $query->where('sender_type', $senderType)
                     ->where('sender_id', $senderId);
    }

    public function scopeForBooking($query, $bookingId)
    {
        return $query->where('booking_id', $bookingId);
    }

    public function scopeThread($query, $parentId = null)
    {
        return $query->where('parent_id', $parentId)
                     ->orderBy('created_at', 'asc');
    }

    // Métodos
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    public function reply($data)
    {
        $reply = $this->replies()->create([
            'sender_type' => $data['sender_type'],
            'sender_id' => $data['sender_id'],
            'receiver_type' => $this->sender_type,
            'receiver_id' => $this->sender_id,
            'accommodation_id' => $this->accommodation_id,
            'booking_id' => $this->booking_id,
            'parent_id' => $this->id,
            'subject' => 'Re: ' . $this->subject,
            'body' => $data['body'],
            'message_type' => $this->message_type,
            'attachments' => $data['attachments'] ?? null,
            'sent_at' => now(),
        ]);

        // Marcar el mensaje original como no leído para el receptor
        $this->markAsUnread();

        return $reply;
    }

    public function getConversation()
    {
        if ($this->parent_id) {
            return $this->parent->getConversation();
        }

        return collect([$this])->merge($this->replies);
    }

    public function hasAttachments()
    {
        return !empty($this->attachments);
    }
}
