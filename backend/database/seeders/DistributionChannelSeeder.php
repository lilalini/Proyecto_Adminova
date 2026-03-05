<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DistributionChannel;

class DistributionChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $channels = [
            [
                'channel_code' => 'booking',
                'name' => 'Booking.com',
                'channel_type' => 'OTA',
                'commission_rate' => 15.00,
                'api_config' => json_encode(['api_key' => 'test_key']),
                'is_active' => true,
                'sync_enabled' => true,
            ],
            [
                'channel_code' => 'airbnb',
                'name' => 'Airbnb',
                'channel_type' => 'OTA',
                'commission_rate' => 14.00,
                'api_config' => json_encode(['api_key' => 'test_key']),
                'is_active' => true,
                'sync_enabled' => true,
            ],
            [
                'channel_code' => 'expedia',
                'name' => 'Expedia',
                'channel_type' => 'OTA',
                'commission_rate' => 18.00,
                'api_config' => json_encode(['api_key' => 'test_key']),
                'is_active' => true,
                'sync_enabled' => true,
            ],
            [
                'channel_code' => 'direct',
                'name' => 'Direct Website',
                'channel_type' => 'direct',
                'commission_rate' => 0,
                'api_config' => null,
                'is_active' => true,
                'sync_enabled' => false,
            ],
        ];

        foreach ($channels as $channel) {
            DistributionChannel::create($channel);
        }
    }
    
}
