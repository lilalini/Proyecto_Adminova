<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ApartmentChannel;
use App\Models\DistributionChannel;
use App\Models\SyncLog;
use App\Models\User;
use Carbon\Carbon;

class SyncLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $apartmentChannels = ApartmentChannel::where('sync_enabled', true)->get();
        $admin = User::where('email', 'admin@example.com')->first();
        
        foreach ($apartmentChannels as $apartmentChannel) {
            // Logs de los últimos 30 días
            $numLogs = rand(5, 15);
            
            for ($i = 0; $i < $numLogs; $i++) {
                $date = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23));
                $status = fake()->randomElement(['success', 'success', 'success', 'warning', 'error']); // 60% success
                
                SyncLog::create([
                    'accommodation_id' => $apartmentChannel->accommodation_id,
                    'channel_id' => $apartmentChannel->distribution_channel_id,
                    'sync_type' => fake()->randomElement(['availability', 'prices', 'bookings']),
                    'direction' => 'both',
                    'status' => $status,
                    'started_at' => $date,
                    'completed_at' => $date->copy()->addSeconds(rand(10, 120)),
                    'duration_seconds' => rand(10, 120),
                    'items_total' => rand(30, 90),
                    'items_success' => $status === 'success' ? 90 : rand(60, 85),
                    'items_failed' => $status === 'success' ? 0 : rand(5, 30),
                    'request_data' => json_encode(['action' => 'sync_all']),
                    'response_data' => json_encode(['message' => 'Sync completed']),
                    'error_message' => $status === 'error' ? 'Connection timeout' : null,
                    'created_by_user_id' => $admin?->id,
                    'created_at' => $date,
                ]);
            }
        }
        
        // Algunos logs sin accommodation específico (sincronizaciones masivas)
        $channels = DistributionChannel::where('is_active', true)->get();
        foreach ($channels as $channel) {
            SyncLog::create([
                'channel_id' => $channel->id,
                'sync_type' => 'content',
                'direction' => 'import',
                'status' => 'success',
                'started_at' => now()->subHours(rand(1, 48)),
                'completed_at' => now()->subHours(rand(1, 48))->addMinutes(5),
                'duration_seconds' => 300,
                'items_total' => rand(50, 200),
                'items_success' => rand(50, 200),
                'request_data' => json_encode(['import_new_listings' => true]),
                'created_by_user_id' => $admin?->id,
            ]);
        }
        
        // Última sincronización exitosa
        foreach ($apartmentChannels as $apartmentChannel) {
            $apartmentChannel->update([
                'last_sync_at' => now()->subHours(rand(1, 12)),
                'last_sync_status' => 'success',
            ]);
        }
    }
}
