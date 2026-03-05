<?php

namespace App\Providers;

use App\Models\Accommodation;
use App\Policies\AccommodationPolicy;
use App\Models\Booking;
use App\Policies\BookingPolicy;
use App\Models\Guest;
use App\Policies\GuestPolicy;
use App\Models\Owner;
use App\Policies\OwnerPolicy;
use App\Models\Payment;
use App\Policies\PaymentPolicy;
use App\Models\Review;
use App\Policies\ReviewPolicy;
use App\Models\OwnerPayoutMethod;
use App\Policies\OwnerPayoutMethodPolicy;
use App\Models\GuestPaymentMethod;
use App\Policies\GuestPaymentMethodPolicy;
use App\Models\AvailabilityCalendar;
use App\Policies\AvailabilityCalendarPolicy;
use App\Models\ApartmentChannel;
use App\Policies\ApartmentChannelPolicy;
use App\Models\LoyaltyPoint;
use App\Policies\LoyaltyPointPolicy;
use App\Models\Commission;
use App\Policies\CommissionPolicy;
use App\Models\CleaningTask;
use App\Policies\CleaningTaskPolicy;
use App\Models\Message;
use App\Policies\MessagePolicy;
use App\Models\Document;
use App\Policies\DocumentPolicy;
use App\Models\Media;
use App\Policies\MediaPolicy;
use App\Models\SyncLog;
use App\Policies\SyncLogPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Accommodation::class => AccommodationPolicy::class,
        Booking::class => BookingPolicy::class,
        Guest::class => GuestPolicy::class,
        Owner::class => OwnerPolicy::class,
        Payment::class => PaymentPolicy::class,
        Review::class => ReviewPolicy::class,
        OwnerPayoutMethod::class => OwnerPayoutMethodPolicy::class,
        GuestPaymentMethod::class => GuestPaymentMethodPolicy::class,
        AvailabilityCalendar::class => AvailabilityCalendarPolicy::class,
        ApartmentChannel::class => ApartmentChannelPolicy::class,
        LoyaltyPoint::class => LoyaltyPointPolicy::class,
        Commission::class => CommissionPolicy::class,
        CleaningTask::class => CleaningTaskPolicy::class,
        Message::class => MessagePolicy::class,
        Document::class => DocumentPolicy::class,
        Media::class => MediaPolicy::class,
        SyncLog::class => SyncLogPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}