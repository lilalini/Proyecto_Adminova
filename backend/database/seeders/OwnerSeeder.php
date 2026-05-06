<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Owner;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class OwnerSeeder extends Seeder
{
        public function run(): void
    {
        Owner::factory(10)->create();

        // Crear user para el owner de prueba
        $user = User::create([
            'first_name' => 'Owner',
            'last_name' => 'Principal',
            'email' => 'owner@example.com',
            'password' => 'password',
            'role' => 'owner',
            'is_active' => true,
            'is_guest' => false,
            'email_verified_at' => now(),
        ]);

        Owner::create([
            'user_id' => $user->id,
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
            'country' => 'ES',
            'iban' => 'ES9121000418450200051332',
            'commission_rate' => 80.00,
            'contract_signed' => true,
            'contract_date' => now(),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

}
