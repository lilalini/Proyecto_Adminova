<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Accommodation;
use App\Models\AvailabilityCalendar;
use Carbon\Carbon;

class AvailabilityCalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accommodations = Accommodation::all();
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addMonths(6);

        foreach ($accommodations as $accommodation) {
            // Generar calendario para los próximos 6 meses
            $currentDate = $startDate->copy();
            
            while ($currentDate <= $endDate) {
                // 80% disponible, 15% reservado, 5% bloqueado/mantenimiento
                $rand = rand(1, 100);
                
                if ($rand <= 80) {
                    $status = 'available';
                } elseif ($rand <= 95) {
                    $status = 'booked';
                } else {
                    $status = 'blocked';
                }
                
                AvailabilityCalendar::create([
                    'accommodation_id' => $accommodation->id,
                    'user_id' => null,
                    'date' => $currentDate->format('Y-m-d'),
                    'status' => $status,
                    'price' => $status === 'blocked' ? null : $accommodation->base_price,
                    'min_nights' => null,
                    'max_nights' => null,
                    'closed_to_arrival' => false,
                    'closed_to_departure' => false,
                ]);
                
                $currentDate->addDay();
            }
            
            // Algunos periodos especiales (puentes, navidad)
            $specialDates = [
                '2024-12-20' => '2025-01-07', // Navidad
                '2025-03-28' => '2025-04-06', // Semana Santa
            ];
            
            foreach ($specialDates as $start => $end) {
                $specialStart = Carbon::parse($start);
                $specialEnd = Carbon::parse($end);
                
                while ($specialStart <= $specialEnd) {
                    if ($specialStart->between($startDate, $endDate)) {
                        // 50% precio especial
                        AvailabilityCalendar::updateOrCreate(
                            [
                                'accommodation_id' => $accommodation->id,
                                'date' => $specialStart->format('Y-m-d'),
                            ],
                            [
                                'price' => $accommodation->base_price * 1.5,
                                'min_nights' => 3,
                            ]
                        );
                    }
                    $specialStart->addDay();
                }
            }
        }
    }
}
