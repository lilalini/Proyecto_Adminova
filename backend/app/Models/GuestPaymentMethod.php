<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class GuestPaymentMethod extends Model
{
    /** @use HasFactory<\Database\Factories\GuestPaymentMethodFactory> */
    use HasFactory, SoftDeletes;

     protected $fillable = [
        'guest_id',
        'method_type', // credit_card, debit_card, paypal, apple_pay, google_pay
        'token', // token de la pasarela (no guardamos datos sensibles)
        'card_last_four',
        'card_brand', // visa, mastercard, amex
        'card_expiry_month',
        'card_expiry_year',
        'is_default',
        'is_expired',
        'last_used_at',
    ];

    protected $hidden = [
        'token', // No exponer el token
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_expired' => 'boolean',
            'last_used_at' => 'datetime',
        ];
    }

    // Relación con Guest
    /** @var \App\Models\Guest $guest */
    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    // Scope para método por defecto
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Scope para métodos no expirados
    public function scopeNotExpired($query)
    {
        return $query->where('is_expired', false);
    }

    // Marcar como usado
    public function markAsUsed()
    {
        $this->update(['last_used_at' => now()]);
    }
}
