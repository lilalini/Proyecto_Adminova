<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    /** @use HasFactory<\Database\Factories\DocumentFactory> */
    use HasFactory, SoftDeletes;

     protected $fillable = [
        'documentable_type', // Accommodation, Owner, Guest, Booking
        'documentable_id',
        'document_type', // contract, id_card, passport, invoice, permit, insurance
        'title',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'is_signed',
        'signed_at',
        'valid_from',
        'valid_until',
        'is_verified',
        'verified_by_user_id',
        'verified_at',
        'notes',
        'metadata', // JSON con datos específicos
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'is_signed' => 'boolean',
            'signed_at' => 'datetime',
            'valid_from' => 'date',
            'valid_until' => 'date',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    // Relación polimórfica
    public function documentable()
    {
        return $this->morphTo();
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    // Scopes
    public function scopeForType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    public function scopeValid($query)
    {
        return $query->where(function($q) {
            $q->whereNull('valid_until')
              ->orWhere('valid_until', '>=', now());
        });
    }

    public function scopeExpired($query)
    {
        return $query->where('valid_until', '<', now());
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopePendingVerification($query)
    {
        return $query->where('is_verified', false);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('valid_until')
                     ->where('valid_until', '>=', now())
                     ->where('valid_until', '<=', now()->addDays($days));
    }

    // Métodos
    public function isValid()
    {
        if ($this->valid_until && $this->valid_until < now()) {
            return false;
        }
        return true;
    }

    public function isExpiringSoon($days = 30)
    {
        return $this->valid_until 
            && $this->valid_until >= now()
            && $this->valid_until <= now()->addDays($days);
    }

    public function markAsVerified($userId)
    {
        $this->update([
            'is_verified' => true,
            'verified_by_user_id' => $userId,
            'verified_at' => now(),
        ]);
    }

    public function markAsSigned()
    {
        $this->update([
            'is_signed' => true,
            'signed_at' => now(),
        ]);
    }

    public function getUrl()
    {
        return asset('storage/' . $this->file_path);
    }

    public function getHumanReadableSize()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
