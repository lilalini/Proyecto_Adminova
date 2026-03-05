<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // NIVEL 1: Independientes
            UserSeeder::class,
            CancellationPolicySeeder::class,
            OwnerSeeder::class,
            GuestSeeder::class,
            LoyaltySettingSeeder::class,
            DistributionChannelSeeder::class,
            SystemSettingSeeder::class,

            // NIVEL 2: Dependen de 1 tabla
            OwnerPayoutMethodSeeder::class,      // necesita owners
            GuestPaymentMethodSeeder::class,     // necesita guests
            AccommodationSeeder::class,          // necesita owners y cancellation_policies
            NotificationSeeder::class,            // necesita users, owners, guests

            // NIVEL 3: Dependen de 2 tablas
            AvailabilityCalendarSeeder::class,    // necesita accommodations
            ApartmentChannelSeeder::class,        // necesita accommodations y distribution_channels
            BookingSeeder::class,                  // necesita accommodations, guests, distribution_channels
            BookingGuestSeeder::class,             // necesita bookings y guests

            // NIVEL 4: Dependen de bookings
            PaymentSeeder::class,                  // necesita bookings
            LoyaltyPointSeeder::class,              // necesita guests, bookings
            CommissionSeeder::class,                 // necesita bookings, channels, accommodations, owners
            ReviewSeeder::class,                     // necesita bookings, accommodations, guests
            CleaningTaskSeeder::class,                // necesita accommodations, bookings, users
            MessageSeeder::class,                      // necesita users, owners, guests, bookings

            // NIVEL 5: Polimórficas y logs
            DocumentSeeder::class,                     // necesita users, owners, guests, accommodations
            MediaSeeder::class,                         // necesita accommodations, owners, guests, users
            SyncLogSeeder::class,                        // necesita apartment_channels
        ]);
    
    }
}
