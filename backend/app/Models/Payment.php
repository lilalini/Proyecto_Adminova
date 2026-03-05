<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;
    
     protected $fillable = [
        'payment_reference',
        'booking_id',
        'guest_id',
        'user_id', // quien registró el pago (staff)
        'payment_type', // deposit, final, full, damage_deposit
        'method', // credit_card, transfer, cash, paypal, stripe
        'transaction_id', // ID de la pasarela
        'amount',
        'currency',
        'status', // pending, completed, failed, refunded
        'payment_date',
        'due_date',
        'notes',
        'receipt_sent',
        'receipt_sent_at',
        'refunded_at',
        'refund_reason',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'datetime',
            'due_date' => 'date',
            'receipt_sent' => 'boolean',
            'receipt_sent_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    // Relaciones
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForBooking($query, $bookingId)
    {
        return $query->where('booking_id', $bookingId);
    }

    // Métodos
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function markAsCompleted($transactionId = null)
    {
        $this->update([
            'status' => 'completed',
            'transaction_id' => $transactionId ?? $this->transaction_id,
            'payment_date' => now(),
        ]);
    }

    public function markAsRefunded($reason = null)
    {
        $this->update([
            'status' => 'refunded',
            'refunded_at' => now(),
            'refund_reason' => $reason,
        ]);
    }

    public function sendReceipt()
    {
        // Enviar email con el recibo
        $this->update([
            'receipt_sent' => true,
            'receipt_sent_at' => now(),
        ]);
    }
}
