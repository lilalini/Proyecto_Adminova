<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['accommodation', 'guest', 'user']);

        // Filtro por accommodation
        if ($request->has('accommodation_id')) {
            $query->where('accommodation_id', $request->accommodation_id);
        }

        // Filtro por rating
        if ($request->has('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        // Solo publicadas para guest
        if ($request->user()?->role === 'guest') {
            $query->where('status', 'published');
        }

        $reviews = $query->paginate(15);
        return ReviewResource::collection($reviews);
    }

    public function store(StoreReviewRequest $request)
    {
        $data = $request->validated();
        
        // Obtener accommodation_id desde booking
        $booking = \App\Models\Booking::find($data['booking_id']);
        $data['accommodation_id'] = $booking->accommodation_id;
        $data['guest_id'] = $request->user()->id;
        $data['status'] = 'pending';
        $data['source'] = $data['source'] ?? 'direct';
        $data['is_verified'] = true;

        $review = Review::create($data);
        $review->load(['accommodation', 'booking', 'guest']);

        return new ReviewResource($review);
    }

    public function show(Review $review)
    {
        $review->load(['accommodation', 'booking', 'guest', 'user']);
        return new ReviewResource($review);
    }

    public function update(UpdateReviewRequest $request, Review $review)
    {
        $review->update($request->validated());
        $review->load(['accommodation', 'booking', 'guest']);

        return new ReviewResource($review);
    }

    public function destroy(Review $review)
    {
        if (!Gate::allows('delete', $review)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $review->delete();
        return response()->json(null, 204);
    }

    // Método adicional para responder a una review
    public function respond(Request $request, Review $review)
    {
        if (!Gate::allows('respond', $review)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'host_response' => 'required|string',
        ]);

        $review->update([
            'host_response' => $request->host_response,
            'host_responded_at' => now(),
            'user_id' => $request->user()->id,
        ]);

        return new ReviewResource($review);
    }
}