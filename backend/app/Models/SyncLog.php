<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage; 

class SyncLog extends Model
{
    /** @use HasFactory<\Database\Factories\SyncLogFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'accommodation_id',
        'channel_id',
        'sync_type', // availability, prices, bookings, content
        'direction', // export, import, both
        'status', // pending, processing, success, warning, error
        'started_at',
        'completed_at',
        'duration_seconds',
        'items_total',
        'items_success',
        'items_failed',
        'request_data', // JSON con lo que se envió
        'response_data', // JSON con lo que se recibió
        'error_message',
        'error_trace',
        'retry_count',
        'next_retry_at',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'duration_seconds' => 'integer',
            'items_total' => 'integer',
            'items_success' => 'integer',
            'items_failed' => 'integer',
            'request_data' => 'array',
            'response_data' => 'array',
            'retry_count' => 'integer',
            'next_retry_at' => 'datetime',
        ];
    }

    // Relaciones
    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function channel()
    {
        return $this->belongsTo(DistributionChannel::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'processing']);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'error');
    }

    public function scopeForChannel($query, $channelId)
    {
        return $query->where('channel_id', $channelId);
    }

    public function scopeForAccommodation($query, $accommodationId)
    {
        return $query->where('accommodation_id', $accommodationId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('sync_type', $type);
    }

    // Métodos
    public function start()
    {
        $this->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);
    }

    public function complete($itemsSuccess = null, $itemsFailed = null)
    {
        $this->update([
            'status' => 'success',
            'completed_at' => now(),
            'duration_seconds' => $this->started_at ? now()->diffInSeconds($this->started_at) : null,
            'items_success' => $itemsSuccess ?? $this->items_success,
            'items_failed' => $itemsFailed ?? $this->items_failed,
        ]);
    }

    public function fail($errorMessage, $errorTrace = null)
    {
        $this->update([
            'status' => 'error',
            'completed_at' => now(),
            'error_message' => $errorMessage,
            'error_trace' => $errorTrace,
            'duration_seconds' => $this->started_at ? now()->diffInSeconds($this->started_at) : null,
        ]);
    }

    public function warning($message)
    {
        $this->update([
            'status' => 'warning',
            'completed_at' => now(),
            'error_message' => $message,
            'duration_seconds' => $this->started_at ? now()->diffInSeconds($this->started_at) : null,
        ]);
    }

    public function incrementRetry()
    {
        $this->increment('retry_count');
        
        // Programar siguiente reintento (exponential backoff)
        $minutes = pow(2, $this->retry_count);
        $this->update([
            'next_retry_at' => now()->addMinutes($minutes),
        ]);
    }

    public function shouldRetry()
    {
        return $this->status === 'error' 
            && $this->retry_count < 5 
            && $this->next_retry_at 
            && $this->next_retry_at <= now();
    }

    public function isSuccessful()
    {
        return $this->status === 'success';
    }

    public function hasErrors()
    {
        return in_array($this->status, ['error', 'warning']);
    }

    public function getSuccessRate()
    {
        if (!$this->items_total) {
            return 0;
        }
        
        return round(($this->items_success / $this->items_total) * 100, 2);
    }

    // Estadísticas
    public static function getFailureRateForChannel($channelId, $days = 7)
    {
        $total = self::forChannel($channelId)
            ->where('created_at', '>=', now()->subDays($days))
            ->count();
            
        if ($total === 0) {
            return 0;
        }
        
        $failed = self::forChannel($channelId)
            ->where('created_at', '>=', now()->subDays($days))
            ->where('status', 'error')
            ->count();
            
        return round(($failed / $total) * 100, 2);
    }
}
