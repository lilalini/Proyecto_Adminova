<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoyaltyPoint extends Model
{
    /** @use HasFactory<\Database\Factories\LoyaltyPointFactory> */
    use HasFactory, SoftDeletes;
     protected $fillable = [
        'guest_id',
        'booking_id',
        'points',
        'type', // earned, redeemed, expired, adjusted
        'description',
        'expiry_date',
        'redeemed_at',
        'redeemed_booking_id', // si se canjearon en otra reserva
        'adjusted_by_user_id', // quien hizo ajuste manual
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'expiry_date' => 'date',
            'redeemed_at' => 'datetime',
        ];
    }

    // Relaciones
    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function redeemedBooking()
    {
        return $this->belongsTo(Booking::class, 'redeemed_booking_id');
    }

    public function adjustedBy()
    {
        return $this->belongsTo(User::class, 'adjusted_by_user_id');
    }

    // Scopes
    public function scopeEarned($query)
    {
        return $query->where('type', 'earned');
    }

    public function scopeRedeemed($query)
    {
        return $query->where('type', 'redeemed');
    }

    public function scopeValid($query)
    {
        return $query->where('type', 'earned')
                     ->where(function($q) {
                         $q->whereNull('redeemed_at')
                           ->where('expiry_date', '>=', now());
                     });
    }

    public function scopeExpired($query)
    {
        return $query->where('type', 'earned')
                     ->whereNull('redeemed_at')
                     ->where('expiry_date', '<', now());
    }

    // Métodos
    public function isValid()
    {
        return $this->type === 'earned' 
            && !$this->redeemed_at 
            && $this->expiry_date >= now();
    }

    public function redeem($bookingId)
    {
        if (!$this->isValid()) {
            return false;
        }

        $this->update([
            'type' => 'redeemed',
            'redeemed_at' => now(),
            'redeemed_booking_id' => $bookingId,
        ]);

        return true;
    }

    public static function getBalance($guestId)
    {
        return self::where('guest_id', $guestId)
            ->valid()
            ->sum('points');
    }
}
