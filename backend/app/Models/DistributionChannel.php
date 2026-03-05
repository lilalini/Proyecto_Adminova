<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DistributionChannel extends Model
{
    /** @use HasFactory<\Database\Factories\DistributionChannelFactory> */
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'channel_code', // booking, airbnb, expedia, direct
        'name', // Booking.com, Airbnb, etc.
        'channel_type', // OTA, direct, corporate
        'commission_rate', // % de comisión
        'api_config', // JSON con credenciales, endpoints, etc.
        'is_active',
        'sync_enabled',
        'last_sync_at',
    ];

    protected $hidden = [
        'api_config', // Ocultar credenciales en respuestas
    ];

    protected function casts(): array
    {
        return [
            'commission_rate' => 'decimal:2',
            'api_config' => 'array',
            'is_active' => 'boolean',
            'sync_enabled' => 'boolean',
            'last_sync_at' => 'datetime',
        ];
    }

    // Relación con apartment_channels (cuando exista)
     public function apartmentChannels()
     {
         return $this->hasMany(ApartmentChannel::class);
     }

    // Scope para canales activos
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope para OTAs
    public function scopeOta($query)
    {
        return $query->where('channel_type', 'OTA');
    }


}
