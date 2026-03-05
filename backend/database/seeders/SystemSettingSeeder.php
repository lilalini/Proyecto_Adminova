<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemSetting::create([
        'key' => 'company_name',
        'value' => 'Mi Gestora',
        'type' => 'string',
        'group' => 'general',
        'description' => 'Nombre de la empresa',
    ]);
    
        SystemSetting::create([
        'key' => 'company_email',
        'value' => 'info@migestora.com',
        'type' => 'string',
        'group' => 'contact',
        'description' => 'Email de contacto',
    ]);
    
        SystemSetting::create([
        'key' => 'company_phone',
        'value' => '+34 123 456 789',
        'type' => 'string',
        'group' => 'contact',
        'description' => 'Teléfono de contacto',
    ]);
    
        SystemSetting::create([
        'key' => 'company_vat',
        'value' => 'B12345678',
        'type' => 'string',
        'group' => 'legal',
        'description' => 'NIF/CIF de la empresa',
    ]);
    
        SystemSetting::create([
        'key' => 'maintenance_mode',
        'value' => 'false',
        'type' => 'boolean',
        'group' => 'system',
        'description' => 'Modo mantenimiento',
    ]);
    }
}
