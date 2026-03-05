<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Owner;
use Illuminate\Support\Facades\Hash;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // 10 owners aleatorios
        Owner::factory(10)->create();
        
        // Owner específico para pruebas
        Owner::create([
            'first_name' => 'Owner',
            'last_name' => 'Principal',
            'email' => 'owner@example.com',
            'password' => Hash::make('123456'),
            'phone' => '123456789',
            'document_type' => 'DNI',
            'document_number' => '12345678A',
            'address' => 'Calle Principal 123',
            'city' => 'Madrid',
            'postal_code' => '28001',
            'country' => 'España',
            'iban' => 'ES9121000418450200051332',
            'contract_signed' => true,
            'contract_date' => now(),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
