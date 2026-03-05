<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Accommodation;
use App\Models\Booking;
use App\Models\CleaningTask;
use App\Models\User;

class CleaningTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accommodations = Accommodation::all();
        $staffUsers = User::where('role', '!=', 'guest')->get();
        
        foreach ($accommodations as $accommodation) {
            // Tareas programadas regulares
            $numTasks = rand(5, 10);
            
            for ($i = 0; $i < $numTasks; $i++) {
                $scheduledDate = fake()->dateTimeBetween('now', '+2 months');
                $booking = Booking::where('accommodation_id', $accommodation->id)
                    ->where('check_out', '>', now())
                    ->inRandomOrder()
                    ->first();
                
                CleaningTask::create([
                    'accommodation_id' => $accommodation->id,
                    'booking_id' => $booking?->id,
                    'assigned_to_user_id' => $staffUsers->random()->id,
                    'created_by_user_id' => $staffUsers->random()->id,
                    'task_type' => fake()->randomElement(['cleaning', 'inspection']),
                    'priority' => 'medium',
                    'title' => 'Limpieza estándar',
                    'scheduled_date' => $booking ? $booking->check_out : $scheduledDate,
                    'status' => fake()->randomElement(['pending', 'completed']),
                ]);
            }
            
            // Tareas de mantenimiento
            if (fake()->boolean(30)) {
                CleaningTask::create([
                    'accommodation_id' => $accommodation->id,
                    'task_type' => 'maintenance',
                    'priority' => 'high',
                    'title' => 'Revisión aire acondicionado',
                    'scheduled_date' => fake()->dateTimeBetween('now', '+1 month'),
                    'status' => 'pending',
                    'assigned_to_user_id' => $staffUsers->random()->id,
                    'created_by_user_id' => $staffUsers->random()->id,
                ]);
            }
        }
        
        // Tareas completadas recientemente
        CleaningTask::where('status', 'pending')
            ->limit(10)
            ->update([
                'status' => 'completed',
                'completed_at' => now()->subDays(rand(1, 7)),
                'duration_minutes' => rand(60, 120),
            ]);
    
    }
}
