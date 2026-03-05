<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Media;

class Accommodation extends Model
{
    /** @use HasFactory<\Database\Factories\AccommodationFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'cancellation_policy_id',
        'title',
        'slug',
        'description',
        'property_type', // apartment, house, studio, room
        'bedrooms',
        'bathrooms',
        'max_guests',
        'size_m2',
        'address',
        'city',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'base_price', // precio por noche
        'weekly_discount', // % descuento semanal
        'monthly_discount', // % descuento mensual
        'cleaning_fee',
        'security_deposit',
        'minimum_stay',
        'maximum_stay',
        'amenities', // JSON: wifi, parking, piscina, etc.
        'house_rules', // JSON: no fiestas, no mascotas, etc.
        'check_in_time',
        'check_out_time',
        'status', // draft, published, maintenance, inactive
        'views',
        'last_booking_at',
    ];

    protected function casts(): array
    {
        return [
            'bedrooms' => 'integer',
            'bathrooms' => 'integer',
            'max_guests' => 'integer',
            'size_m2' => 'integer',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'base_price' => 'decimal:2',
            'weekly_discount' => 'decimal:2',
            'monthly_discount' => 'decimal:2',
            'cleaning_fee' => 'decimal:2',
            'security_deposit' => 'decimal:2',
            'minimum_stay' => 'integer',
            'maximum_stay' => 'integer',
            'amenities' => 'array',
            'house_rules' => 'array',
            'check_in_time' => 'string',
            'check_out_time' => 'string',
            'status' => 'string',
            'views' => 'integer',
            'last_booking_at' => 'datetime',
        ];
    }

    // Relaciones (comentadas hasta que existan los modelos)

    public function media()
    {
        return $this->morphMany(Media::class, 'model');
    }
    
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function cancellationPolicy()
    {
        return $this->belongsTo(CancellationPolicy::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function availabilityCalendar()
    {
        return $this->hasMany(AvailabilityCalendar::class);
    }

    public function apartmentChannels()
    {
        return $this->hasMany(ApartmentChannel::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function cleaningTasks()
    {
        return $this->hasMany(CleaningTask::class);
    }
    

    // Scopes útiles
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeInCity($query, $city)
    {
        return $query->where('city', $city);
    }

    public function scopeForGuests($query, $guests)
    {
        return $query->where('max_guests', '>=', $guests);
    }

    public function scopeWithAmenities($query, array $amenities)
    {
        foreach ($amenities as $amenity) {
            $query->whereJsonContains('amenities', $amenity);
        }
        return $query;
    }
}
