<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['accommodation', 'guest', 'user']);

        if ($request->has('accommodation_id')) {
            $query->where('accommodation_id', $request->accommodation_id);
        }

        if ($request->has('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        if ($request->user()?->role === 'guest') {
            $query->where('status', 'published');
        }

        return ReviewResource::collection($query->paginate(15));
    }

    public function store(StoreReviewRequest $request)
    {
        $data = $request->validated();

        $booking = Booking::find($data['booking_id']);
        $data['accommodation_id'] = $booking->accommodation_id;
        $data['guest_id'] = $request->user()->guest?->id;
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
        $this->authorize('update', $review);
        $review->update($request->validated());
        $review->load(['accommodation', 'booking', 'guest']);
        return new ReviewResource($review);
    }

    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);
        $review->delete();
        return response()->json(null, 204);
    }

    public function respond(Request $request, Review $review)
    {
        $this->authorize('respond', $review);

        $request->validate([
            'host_response' => 'required|string',
        ]);

        $review->respond($request->host_response, $request->user()->id);

        return new ReviewResource($review->fresh());
    }

    public function publicIndex($accommodationId)
    {
        $reviews = Review::with('guest')
            ->where('accommodation_id', $accommodationId)
            ->where('status', 'published')
            ->latest()
            ->paginate(10);

        return response()->json(['data' => $reviews]);
    }
}