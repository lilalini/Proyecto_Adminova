<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApartmentChannel extends Model
{
    /** @use HasFactory<\Database\Factories\ApartmentChannelFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'accommodation_id',
        'distribution_channel_id',
        'external_listing_id', // ID del alojamiento en la OTA
        'external_url', // URL del anuncio
        'connection_status', // connected, disconnected, error
        'sync_enabled',
        'sync_price', // sincronizar precios?
        'sync_availability', // sincronizar disponibilidad?
        'sync_content', // sincronizar fotos/descripciones?
        'price_adjustment_type', // percentage, fixed
        'price_adjustment_value', // +10%, -5€, etc.
        'min_stay_adjustment', // días mínimos específicos para este canal
        'last_sync_at',
        'last_sync_status',
        'last_sync_message',
        'channel_data', // JSON con datos específicos del canal
    ];

    protected function casts(): array
    {
        return [
            'sync_enabled' => 'boolean',
            'sync_price' => 'boolean',
            'sync_availability' => 'boolean',
            'sync_content' => 'boolean',
            'price_adjustment_value' => 'decimal:2',
            'min_stay_adjustment' => 'integer',
            'last_sync_at' => 'datetime',
            'channel_data' => 'array',
        ];
    }

    // Relaciones
    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function distributionChannel()
    {
        return $this->belongsTo(DistributionChannel::class);
    }

    // Scopes
    public function scopeConnected($query)
    {
        return $query->where('connection_status', 'connected');
    }

    public function scopeSyncEnabled($query)
    {
        return $query->where('sync_enabled', true);
    }

    // Métodos útiles
    public function isConnected()
    {
        return $this->connection_status === 'connected';
    }

    public function markAsSynced($status = 'success', $message = null)
    {
        $this->update([
            'last_sync_at' => now(),
            'last_sync_status' => $status,
            'last_sync_message' => $message,
        ]);
    }
}
