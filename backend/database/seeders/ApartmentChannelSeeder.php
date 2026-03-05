<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Accommodation;
use App\Models\DistributionChannel;
use App\Models\ApartmentChannel;

class ApartmentChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accommodations = Accommodation::where('status', 'published')->get();
        $channels = DistributionChannel::where('is_active', true)->get();

        foreach ($accommodations as $accommodation) {
            // Cada propiedad en 2-4 canales
            $selectedChannels = $channels->random(min(rand(2, 4), $channels->count()));
            
            foreach ($selectedChannels as $channel) {
                // Ajuste diferente según canal
                if ($channel->channel_type === 'OTA') {
                    $adjustmentType = 'percentage';
                    $adjustmentValue = 15.00; // +15% en OTAs para cubrir comisión
                } else {
                    $adjustmentType = 'none';
                    $adjustmentValue = null;
                }
                
                ApartmentChannel::create([
                    'accommodation_id' => $accommodation->id,
                    'distribution_channel_id' => $channel->id,
                    'external_listing_id' => $channel->channel_code . '_' . $accommodation->id,
                    'connection_status' => 'connected',
                    'sync_enabled' => true,
                    'sync_price' => true,
                    'sync_availability' => true,
                    'sync_content' => false,
                    'price_adjustment_type' => $adjustmentType,
                    'price_adjustment_value' => $adjustmentValue,
                    'last_sync_at' => now()->subHours(rand(1, 48)),
                    'last_sync_status' => 'success',
                ]);
            }
        }
        
        // Canal directo siempre conectado
        $directChannel = DistributionChannel::where('channel_code', 'direct')->first();
        if ($directChannel) {
            foreach ($accommodations as $accommodation) {
                ApartmentChannel::firstOrCreate(
                    [
                        'accommodation_id' => $accommodation->id,
                        'distribution_channel_id' => $directChannel->id,
                    ],
                    [
                        'connection_status' => 'connected',
                        'sync_enabled' => true,
                        'price_adjustment_type' => 'none',
                    ]
                );
            }
        }
    
    }
}
