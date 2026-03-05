<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OwnerPayoutMethod extends Model
{
    /** @use HasFactory<\Database\Factories\OwnerPayoutMethodFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'method_type', // bank_transfer, paypal, stripe, wise
        'account_holder',
        'account_number', // IBAN, email paypal, etc.
        'bank_name',
        'bank_swift',
        'is_default',
        'is_verified',
        'verified_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    // Relación con Owner
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    // Scope para método por defecto
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Scope para métodos verificados
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}
