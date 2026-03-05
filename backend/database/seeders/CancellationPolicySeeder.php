<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CancellationPolicy;

class CancellationPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // Políticas predefinidas
        $policies = [
            [
                'name' => 'Flexible',
                'description' => 'Cancelación gratuita hasta 24h antes',
                'free_cancellation_days' => 1,
                'penalty_percentage' => 0,
                'is_default' => true,
            ],
            [
                'name' => 'Moderada',
                'description' => 'Cancelación gratuita hasta 7 días antes',
                'free_cancellation_days' => 7,
                'penalty_percentage' => 50,
                'is_default' => false,
            ],
            [
                'name' => 'Estricta',
                'description' => 'Cancelación gratuita hasta 30 días antes',
                'free_cancellation_days' => 30,
                'penalty_percentage' => 100,
                'is_default' => false,
            ],
            [
                'name' => 'No reembolsable',
                'description' => 'No se realizan reembolsos',
                'free_cancellation_days' => 0,
                'penalty_percentage' => 100,
                'is_default' => false,
            ],
        ];

        foreach ($policies as $policy) {
            CancellationPolicy::create($policy);
        }
    }
    
}
