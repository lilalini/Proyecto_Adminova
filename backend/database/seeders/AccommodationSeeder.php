<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Accommodation;
use App\Models\Owner;
use App\Models\CancellationPolicy;

class AccommodationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $owners = Owner::all();
        $policies = CancellationPolicy::all();

        foreach ($owners as $owner) {
            // 2-5 propiedades por owner
            $numProperties = rand(2, 5);
            
            for ($i = 0; $i < $numProperties; $i++) {
                Accommodation::factory()->create([
                    'owner_id' => $owner->id,
                    'cancellation_policy_id' => $policies->random()->id,
                ]);
            }
        }
        
        // Propiedad específica para pruebas
        Accommodation::create([
            'owner_id' => Owner::where('email', 'owner@example.com')->first()->id ?? Owner::first()->id,
            'cancellation_policy_id' => CancellationPolicy::where('name', 'Flexible')->first()->id,
            'title' => 'Ático de Lujo Centro',
            'slug' => 'atico-lujo-centro',
            'description' => 'Espectacular ático en el centro con terraza y vistas',
            'property_type' => 'apartment',
            'bedrooms' => 3,
            'bathrooms' => 2,
            'max_guests' => 6,
            'size_m2' => 120,
            'address' => 'Gran Vía 123',
            'city' => 'Madrid',
            'postal_code' => '28013',
            'country' => 'ES',
            'base_price' => 250.00,
            'cleaning_fee' => 50.00,
            'status' => 'published',
        ]);
    
    }
}
