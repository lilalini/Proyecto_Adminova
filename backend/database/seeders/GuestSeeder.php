<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Guest;

class GuestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // 20 huéspedes aleatorios
        Guest::factory(20)->create();
        
        // Huésped específico para pruebas
        Guest::create([
            'first_name' => 'Guest',
            'last_name' => 'Prueba',
            'email' => 'guest@example.com',
            'phone' => '123456789',
            'document_type' => 'DNI',
            'document_number' => '87654321B',
            'nationality' => 'ES',
            'birth_date' => '1990-01-01',
            'gender' => 'male',
            'address' => 'Calle Secundaria 456',
            'city' => 'Barcelona',
            'postal_code' => '08001',
            'country' => 'España',
            'source' => 'direct',
        ]);
    }
    
}
