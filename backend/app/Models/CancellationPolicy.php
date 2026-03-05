<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CancellationPolicy extends Model
{
    /** @use HasFactory<\Database\Factories\CancellationPolicyFactory> */
    use HasFactory, SoftDeletes;

     protected $fillable = [
        'name', // Flexible, Estricta, No reembolsable
        'description',
        'free_cancellation_days', // días antes del check-in con cancelación gratis
        'penalty_percentage', // % de penalización si cancela después
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'free_cancellation_days' => 'integer',
            'penalty_percentage' => 'decimal:2',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // Relación con accommodations (la crearemos cuando exista el modelo)
    public function accommodations()
    {
         return $this->hasMany(Accommodation::class);
    }

    // Scope para política por defecto
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Scope para activas
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
