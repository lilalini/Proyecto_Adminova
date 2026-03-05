<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    /** @use HasFactory<\Database\Factories\BookingFactory> */
    use HasFactory;

    protected $fillable = [
        'booking_reference', // código único para el cliente
        'accommodation_id',
        'channel_id', // distribution_channel_id
        'guest_id', // puede ser null si viene de OTA sin guest completo
        'guest_temporal_id', // para OTAs con datos parciales (si usamos)
        
        // Fechas
        'check_in',
        'check_out',
        'nights',
        
        // Ocupación
        'adults',
        'children',
        'infants',
        'pets',
        
        // Origen y estado
        'source', // direct, booking, airbnb, expedia
        'status', // pending, confirmed, checked_in, checked_out, cancelled, no_show
        
        // Precios (snapshot en el momento de la reserva)
        'price_per_night',
        'base_price',
        'cleaning_fee',
        'service_fee',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'balance_due',
        
        // Comisiones (snapshot)
        'channel_commission_rate',
        'channel_commission_amount',
        'platform_fee',
        
        // Métodos de pago
        'currency',
        'payment_status', // pending, partial, paid, refunded
        'payment_due_date',
        
        // Datos del cliente en el momento de la reserva
        'guest_name',
        'guest_email',
        'guest_phone',
        'guest_data', // JSON con todos los datos del titular
        
        // Notas
        'guest_notes',
        'staff_notes',
        'cancellation_reason',
        
        // Fechas clave
        'confirmed_at',
        'checked_in_at',
        'checked_out_at',
        'cancelled_at',
        'cancelled_by_user_id',
        
        // Metadatos
        'ip_address',
        'user_agent',
        'locale',
    ];

    protected function casts(): array
    {
        return [
            'check_in' => 'date',
            'check_out' => 'date',
            'nights' => 'integer',
            'adults' => 'integer',
            'children' => 'integer',
            'infants' => 'integer',
            'pets' => 'integer',
            'price_per_night' => 'decimal:2',
            'base_price' => 'decimal:2',
            'cleaning_fee' => 'decimal:2',
            'service_fee' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance_due' => 'decimal:2',
            'channel_commission_rate' => 'decimal:2',
            'channel_commission_amount' => 'decimal:2',
            'platform_fee' => 'decimal:2',
            'guest_data' => 'array',
            'confirmed_at' => 'datetime',
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'payment_due_date' => 'date',
        ];
    }

    // Relaciones
    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function channel()
    {
        return $this->belongsTo(DistributionChannel::class, 'channel_id');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by_user_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function cleaningTasks()
    {
        return $this->hasMany(CleaningTask::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
    // Acompañantes (a través de tabla pivote)
    public function companions()
    {
        return $this->belongsToMany(Guest::class, 'booking_guest')
                    ->withPivot(['type', 'first_name', 'last_name', 'document_type', 
                                'document_number', 'birth_date', 'legal_data_completed'])
                    ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['confirmed', 'checked_in']);
    }

    public function scopeForPeriod($query, $start, $end)
    {
        return $query->where(function($q) use ($start, $end) {
            $q->whereBetween('check_in', [$start, $end])
              ->orWhereBetween('check_out', [$start, $end]);
        });
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePendingPayment($query)
    {
        return $query->where('balance_due', '>', 0)
                     ->where('payment_status', 'pending');
    }

    // Métodos de estado
    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

    public function isCheckedIn()
    {
        return $this->status === 'checked_in';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']) 
            && $this->check_in > now();
    }

    public function markAsCheckedIn()
    {
        $this->update([
            'status' => 'checked_in',
            'checked_in_at' => now(),
        ]);
    }

    public function markAsCheckedOut()
    {
        $this->update([
            'status' => 'checked_out',
            'checked_out_at' => now(),
        ]);
    }

    public function calculateBalance()
    {
        return $this->total_amount - $this->paid_amount;
    }
}
