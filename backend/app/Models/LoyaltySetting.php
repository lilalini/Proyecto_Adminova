<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoyaltySetting extends Model
{
    /** @use HasFactory<\Database\Factories\LoyaltySettingFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', // ej: "Programa estándar 2026"
        'points_per_currency', // puntos por euro gastado
        'points_to_currency_ratio', // valor de cada punto en euros
        'min_redemption', // puntos mínimos para canjear
        'expiry_days', // días de validez de los puntos
        'max_discount', // % máximo de descuento con puntos
        'is_active',
        'valid_from',
        'valid_until',
    ];

    protected function casts(): array
    {
        return [
            'points_per_currency' => 'decimal:2',
            'points_to_currency_ratio' => 'decimal:2',
            'min_redemption' => 'integer',
            'expiry_days' => 'integer',
            'max_discount' => 'decimal:2',
            'is_active' => 'boolean',
            'valid_from' => 'date',
            'valid_until' => 'date',
        ];
    }

    // Scope para configuración activa actual
    public function scopeCurrent($query)
    {
        return $query->where('is_active', true)
                     ->where('valid_from', '<=', now())
                     ->where(function($q) {
                         $q->where('valid_until', '>=', now())
                           ->orWhereNull('valid_until');
                     });
    }
}
