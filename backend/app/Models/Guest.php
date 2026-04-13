<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    /** @use HasFactory<\Database\Factories\GuestFactory> */
    use HasFactory;

     protected $fillable = [
        'user_id', 
        'first_name',
        'last_name',
        'email',
        'phone',
        'document_type',
        'document_number',
        'nationality',
        'birth_date',
        'gender',
        'address',
        'city',
        'postal_code',
        'country',
        'source', // direct, booking, airbnb, expedia
        'source_data', // JSON con datos raw de la OTA
        'external_id', // ID en la OTA
        'accepts_newsletter', 
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'gender' => 'string',
            'source_data' => 'array',
        ];
    }

    // Relación con reservas a través de la tabla pivote
    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_guest')
                    ->withPivot(['type', 'first_name', 'last_name', 'document_type', 
                                'document_number', 'birth_date', 'legal_data_completed', 
                                'legal_data_completed_at', 'completed_by_user_id'])
                    ->withTimestamps();
    }

    // Reservas donde es el titular (main)
    public function mainBookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_guest')
                    ->wherePivot('type', 'main')
                    ->withTimestamps();
    }

    // Reservas donde es acompañante
    public function companionBookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_guest')
                    ->wherePivot('type', 'companion')
                    ->withTimestamps();
    }

    // Scope para buscar por email o documento (evitar duplicados)
    public function scopeFindDuplicate($query, $email, $documentNumber)
    {
        return $query->where(function($q) use ($email, $documentNumber) {
            $q->where('email', $email)
              ->orWhere('document_number', $documentNumber);
        });
    }
        
     // Scope para guests activos ACORDARSE DE USARLO EN LOS CONTROLADORES
    public function scopeActive($query)
    {
        return $query->whereHas('bookings', function($q) {
            $q->where('status', 'confirmed')
            ->where('check_in', '>', now());
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
