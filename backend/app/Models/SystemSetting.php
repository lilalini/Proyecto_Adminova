<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemSetting extends Model
{
    /** @use HasFactory<\Database\Factories\SystemSettingFactory> */
    use HasFactory, SoftDeletes;

     protected $fillable = [
        'key', // company_name, email, phone, address, etc.
        'value',
        'type', // string, boolean, integer, json, image
        'group', // general, payments, emails, appearance
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'json', // para poder guardar cualquier tipo
            'is_active' => 'boolean',
        ];
    }

    // Obtener valor por key
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)
                       ->where('is_active', true)
                       ->first();
        
        if (!$setting) {
            return $default;
        }

        // Convertir según el tipo
        return match($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    // Scope para grupo
    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
    }
}
