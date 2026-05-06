<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccommodationRequest;
use App\Http\Requests\UpdateAccommodationRequest;
use App\Http\Resources\AccommodationResource;
use App\Models\Accommodation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests\AvailabilityRequest;
use App\Services\GeocodingService;
use App\Models\Owner;

class AccommodationController extends Controller
{
    public function __construct(
        protected GeocodingService $geocodingService
    ) {}

    public function index(Request $request)
    {
        $query = Accommodation::query();
        $user = $request->user();

        if ($user->role === 'owner') {
            $owner = Owner::where('user_id', $user->id)->first();
            if ($owner) {
                $query->where('owner_id', $owner->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->role === 'guest') { 
            $query->where('status', 'published');
        }

        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->has('status') && $user->role !== 'guest') {
            $query->where('status', $request->status);
        }

        if ($request->has('min_price')) {
            $query->where('base_price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('base_price', '<=', $request->max_price);
        }

        if ($request->has('guests')) {
            $query->where('max_guests', '>=', $request->guests);
        }

        $query->with(['owner', 'cancellationPolicy', 'media'])->orderBy('id', 'desc');

        return AccommodationResource::collection($query->paginate(10));
    }

    public function store(StoreAccommodationRequest $request)
    {
        $this->authorize('create', Accommodation::class);

        $validated = $request->validated();
        $user = $request->user();

        if ($user->role === 'owner') {
            $validated['owner_id'] = $user->id;
        } else {
            // admin — owner_id viene validado desde el request
            $validated['owner_id'] = $request->owner_id ?? $user->id;
        }

        // Slug único con sufijo
        $base = Str::slug($validated['title']);
        $validated['slug'] = $base . '-' . Str::lower(Str::random(6));

        if (empty($validated['latitude']) || empty($validated['longitude'])) {
            $address = trim($validated['address']) . ', ' . trim($validated['city']) . ', ' . trim($validated['country']);
            $coordinates = $this->geocodingService->geocodeAddress($address);

            if ($coordinates) {
                $validated['latitude'] = $coordinates['lat'];
                $validated['longitude'] = $coordinates['lon'];
            }
        }

        $accommodation = Accommodation::create($validated);
        $accommodation->load(['owner', 'cancellationPolicy']);

        return new AccommodationResource($accommodation);
    }

    public function show(Accommodation $accommodation)
    {
        $this->authorize('view', $accommodation);
        $accommodation->load(['owner', 'cancellationPolicy', 'media']);
        return new AccommodationResource($accommodation);
    }

    public function update(UpdateAccommodationRequest $request, Accommodation $accommodation)
    {
        $this->authorize('update', $accommodation);

        $validated = $request->validated();

        if (isset($validated['title'])) {
            $base = Str::slug($validated['title']);
            $validated['slug'] = $base . '-' . Str::lower(Str::random(6));
        }

        $needsGeocoding = (empty($validated['latitude']) && empty($validated['longitude']))
            || isset($validated['address']) || isset($validated['city']) || isset($validated['country']);

        if ($needsGeocoding) {
            $address = implode(', ', array_filter([
                $validated['address'] ?? $accommodation->address,
                $validated['city'] ?? $accommodation->city,
                $validated['country'] ?? $accommodation->country,
            ]));

            $coordinates = $this->geocodingService->geocodeAddress($address);
            if ($coordinates) {
                $validated['latitude'] = $coordinates['lat'];
                $validated['longitude'] = $coordinates['lon'];
            }
        }

        $accommodation->update($validated);
        $accommodation->load(['owner', 'cancellationPolicy', 'media']);

        return new AccommodationResource($accommodation);
    }

    public function destroy(Accommodation $accommodation)
    {
        $this->authorize('delete', $accommodation);
        $accommodation->delete();
        return response()->json(null, 204);
    }

    public function availability(AvailabilityRequest $request, Accommodation $accommodation)
    {
        $this->authorize('view', $accommodation);

        $unavailableDates = $accommodation->availabilityCalendars()
            ->whereBetween('date', [$request->check_in, $request->check_out])
            ->whereIn('status', ['booked', 'blocked', 'maintenance'])
            ->get();

        if ($unavailableDates->isEmpty()) {
            return response()->json(['available' => true]);
        }

        return response()->json([
            'available' => false,
            'conflicts' => $unavailableDates->pluck('date'),
        ]);
    }

    public function publicList(Request $request)
    {
        $query = Accommodation::where('status', 'published')
            ->with(['owner', 'media']);

        $sortMap = [
            'newest'        => ['created_at', 'desc'],
            'price_asc'     => ['base_price', 'asc'],
            'price_desc'    => ['base_price', 'desc'],
            'bedrooms_asc'  => ['bedrooms', 'asc'],
            'bedrooms_desc' => ['bedrooms', 'desc'],
        ];

        [$col, $dir] = $sortMap[$request->sort] ?? ['created_at', 'desc'];
        $query->orderBy($col, $dir);

        if ($request->has('city')) {
            $query->whereRaw('unaccent(city) ILIKE unaccent(?)', ['%' . $request->city . '%']);
        }

        if ($request->has('guests')) {
            $query->where('max_guests', '>=', $request->guests);
        }

        if ($request->has('check_in') && $request->has('check_out')) {
            // TODO: implementar filtro de disponibilidad
            return response()->json(['message' => 'Filtro de disponibilidad no implementado'], 501);
        }

        return AccommodationResource::collection($query->paginate(6));
    }

    public function publicShow($id)
    {
        $accommodation = Accommodation::with([
            'owner',
            'media',
            'reviews' => fn($q) => $q->where('status', 'published')
                ->with('guest')
                ->latest()
                ->limit(10),
        ])->findOrFail($id);

        if ($accommodation->status !== 'published') {
            return response()->json(['message' => 'Alojamiento no disponible'], 404);
        }

        return new AccommodationResource($accommodation);
    }
}