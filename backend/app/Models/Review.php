<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory, SoftDeletes;

     protected $fillable = [
        'accommodation_id',
        'booking_id',
        'guest_id',
        'user_id', // quien respondió (staff)
        'rating', // 1-5
        'cleanliness_rating', // 1-5
        'communication_rating', // 1-5
        'location_rating', // 1-5
        'value_rating', // 1-5
        'title',
        'comment',
        'host_response',
        'host_responded_at',
        'status', // pending, published, rejected, archived
        'source', // direct, booking, airbnb, google
        'external_review_id', // ID en la OTA
        'is_verified',
        'helpful_votes',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'cleanliness_rating' => 'integer',
            'communication_rating' => 'integer',
            'location_rating' => 'integer',
            'value_rating' => 'integer',
            'host_responded_at' => 'datetime',
            'is_verified' => 'boolean',
            'helpful_votes' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    // Relaciones
    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeHighRated($query, $minRating = 4)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function scopeForAccommodation($query, $accommodationId)
    {
        return $query->where('accommodation_id', $accommodationId);
    }

    // Métodos
    public function isPublished()
    {
        return $this->status === 'published';
    }

    public function publish()
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function reject()
    {
        $this->update(['status' => 'rejected']);
    }

    public function respond($response, $userId)
    {
        $this->update([
            'host_response' => $response,
            'host_responded_at' => now(),
            'user_id' => $userId,
        ]);
    }

    public function getAverageRating()
    {
        $ratings = [
            $this->cleanliness_rating,
            $this->communication_rating,
            $this->location_rating,
            $this->value_rating,
        ];
        
        $ratings = array_filter($ratings); // quitar nulos
        
        return count($ratings) > 0 
            ? array_sum($ratings) / count($ratings) 
            : $this->rating;
    }

    public function markAsHelpful()
    {
        $this->increment('helpful_votes');
    }
}
