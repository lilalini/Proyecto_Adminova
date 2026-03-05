<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OwnerPayoutMethod;
use App\Models\Owner;

class OwnerPayoutMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $owners = Owner::all();

        foreach ($owners as $owner) {
            // 1-3 métodos por owner
            $numMethods = rand(1, 3);
            
            for ($i = 0; $i < $numMethods; $i++) {
                OwnerPayoutMethod::factory()->create([
                    'owner_id' => $owner->id,
                    'is_default' => $i === 0, // solo el primero es default
                ]);
            }
        }
    }
}
