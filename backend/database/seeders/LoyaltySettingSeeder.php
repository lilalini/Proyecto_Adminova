<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LoyaltySetting;

class LoyaltySettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'name' => 'Bronze',
                'points_per_currency' => 5.00,
                'points_to_currency_ratio' => 0.01,
                'min_redemption' => 100,
                'expiry_days' => 365,
                'max_discount' => 10.00,
                'is_active' => true,
            ],
            [
                'name' => 'Silver',
                'points_per_currency' => 10.00,
                'points_to_currency_ratio' => 0.015,
                'min_redemption' => 200,
                'expiry_days' => 365,
                'max_discount' => 15.00,
                'is_active' => true,
            ],
            [
                'name' => 'Gold',
                'points_per_currency' => 15.00,
                'points_to_currency_ratio' => 0.02,
                'min_redemption' => 300,
                'expiry_days' => 365,
                'max_discount' => 20.00,
                'is_active' => true,
            ],
            [
                'name' => 'Platinum',
                'points_per_currency' => 20.00,
                'points_to_currency_ratio' => 0.025,
                'min_redemption' => 500,
                'expiry_days' => 365,
                'max_discount' => 25.00,
                'is_active' => true,
            ],
        ];

        foreach ($settings as $setting) {
            LoyaltySetting::create($setting);
        }
    }
}
