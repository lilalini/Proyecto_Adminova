<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GuestPaymentMethod;
use App\Models\Guest;

class GuestPaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $guests = Guest::all();

        foreach ($guests as $guest) {
            // 1-2 métodos por guest (30% de los guests tienen)
            if (fake()->boolean(70)) {
                $numMethods = rand(1, 2);
                
                for ($i = 0; $i < $numMethods; $i++) {
                    GuestPaymentMethod::factory()->create([
                        'guest_id' => $guest->id,
                        'is_default' => $i === 0, // primero es default
                    ]);
                }
            }
        }
    
    }
}
