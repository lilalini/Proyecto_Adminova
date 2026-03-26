<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AccommodationController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\GuestController;
use App\Http\Controllers\Api\OwnerController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\OwnerPayoutMethodController;
use App\Http\Controllers\Api\GuestPaymentMethodController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\AvailabilityCalendarController;
use App\Http\Controllers\Api\ApartmentChannelController;
use App\Http\Controllers\Api\LoyaltyPointController;
use App\Http\Controllers\Api\CommissionController;
use App\Http\Controllers\Api\CleaningTaskController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\SyncLogController;

// Ruta de prueba (pública)
Route::get('/test', function() {
    return response()->json([
        'message' => 'API funcionando correctamente',
        'status' => 'ok'
    ]);
});

    // Rutas públicas   
    Route::get('/accommodations/public', [App\Http\Controllers\Api\AccommodationController::class, 'publicList']);
    Route::get('/accommodations/{id}/public', [App\Http\Controllers\Api\AccommodationController::class, 'publicShow']);
    Route::get('/availability/public/{accommodationId}', [App\Http\Controllers\Api\AvailabilityCalendarController::class, 'publicIndex']);
    Route::get('/reviews/public/{accommodationId}', [App\Http\Controllers\Api\ReviewController::class, 'publicIndex']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas (todas en un solo grupo)
    Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

// Media de alojamientos 
    Route::post('/accommodations/{accommodation}/media', [App\Http\Controllers\Api\AccommodationMediaController::class, 'store']);
    Route::delete('/accommodations/{accommodation}/media/{mediaId}', [App\Http\Controllers\Api\AccommodationMediaController::class, 'destroy']);
    // Rutas de reservas
    Route::get('/bookings/my', [BookingController::class, 'myBookings']);
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancelByGuest']);
    // CRUDs
    Route::apiResource('accommodations', AccommodationController::class);
    Route::apiResource('bookings', BookingController::class);
    Route::apiResource('guests', GuestController::class);
    Route::apiResource('owners', OwnerController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::apiResource('reviews', ReviewController::class);
    // Ruta extra para responder reviews
    Route::post('/reviews/{review}/respond', [ReviewController::class, 'respond']);
    Route::apiResource('owner-payout-methods', OwnerPayoutMethodController::class);
    Route::apiResource('guest-payment-methods', GuestPaymentMethodController::class);
     // Notifications
    Route::get('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead']);
    Route::apiResource('notifications', NotificationController::class)->only([
        'index', 'show', 'destroy'
    ]);
    // Rutas extra
    Route::post('/bookings/{booking}/confirm-payment', [BookingController::class, 'confirmPayment']);
    Route::post('/availability/update-range', [AvailabilityCalendarController::class, 'updateRange']);
    Route::apiResource('availability', AvailabilityCalendarController::class);
    Route::post('/apartment-channels/{apartment_channel}/sync', [ApartmentChannelController::class, 'sync']);
    Route::apiResource('apartment-channels', ApartmentChannelController::class);
    Route::get('/loyalty-points/balance/{guestId}', [LoyaltyPointController::class, 'balance']);
    Route::apiResource('loyalty-points', LoyaltyPointController::class)->except(['store']);
    Route::post('/commissions/{commission}/mark-paid', [CommissionController::class, 'markAsPaid']);
    Route::apiResource('commissions', CommissionController::class)->except(['store']);
    Route::post('/cleaning-tasks/{cleaning_task}/verify', [CleaningTaskController::class, 'verify']);
    Route::post('/cleaning-tasks/{cleaning_task}/assign', [CleaningTaskController::class, 'assign']);
    Route::apiResource('cleaning-tasks', CleaningTaskController::class);
    Route::get('/messages/conversations', [MessageController::class, 'conversations']);
    Route::post('/messages/{message}/mark-read', [MessageController::class, 'markAsRead']);
    Route::apiResource('messages', MessageController::class)->except(['update']);
    Route::post('/documents/{document}/verify', [DocumentController::class, 'verify']);
    Route::get('/documents/{document}/download', [DocumentController::class, 'download']);
    Route::apiResource('documents', DocumentController::class);
    Route::post('/media/reorder', [MediaController::class, 'reorder']);
    Route::post('/media/{media}/set-main', [MediaController::class, 'setMain']);
    Route::apiResource('media', MediaController::class);
    Route::apiResource('sync-logs', SyncLogController::class)->only(['index', 'show', 'destroy']);
});