<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AvailabilityCalendar extends Model
{
    /** @use HasFactory<\Database\Factories\AvailabilityCalendarFactory> */
    use HasFactory, SoftDeletes;

     protected $table = 'availability_calendars';

     protected $fillable = [
        'accommodation_id',
        'user_id',
        'date',
        'status', // available, booked, blocked, maintenance
        'price',
        'min_nights',
        'max_nights',
        'closed_to_arrival',
        'closed_to_departure',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'price' => 'decimal:2',
            'min_nights' => 'integer',
            'max_nights' => 'integer',
            'closed_to_arrival' => 'boolean',
            'closed_to_departure' => 'boolean',
        ];
    }

    // Relaciones
    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes de consulta (útiles en controllers)
    public function scopeForPeriod($query, $start, $end)
    {
        return $query->whereBetween('date', [$start, $end]);
    }

    public function scopeUnavailable($query)
    {
        return $query->whereIn('status', ['booked', 'blocked', 'maintenance']);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    // Métodos de verificación (útiles en modelos)
    public function isAvailable()
    {
        return $this->status === 'available';
    }

    public function isBooked()
    {
        return $this->status === 'booked';
    }

    public function isBlocked()
    {
        return in_array($this->status, ['blocked', 'maintenance']);
    }
}
