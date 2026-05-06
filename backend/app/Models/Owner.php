<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'document_type',
        'document_number',
        'address',
        'city',
        'postal_code',
        'country',
        'iban',
        'commission_rate',
        'contract_signed',
        'contract_date',
        'is_active',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'contract_signed' => 'boolean',
            'is_active' => 'boolean',
            'contract_date' => 'date',
            'last_login_at' => 'datetime',
            'commission_rate' => 'decimal:2',
        ];
    }

    // Relación con accommodations (sus propiedades)
    
    public function accommodations()
    {
        return $this->hasMany(Accommodation::class);
    }

    // Scope para owners activos
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}