<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccommodationRequest;
use App\Http\Requests\UpdateAccommodationRequest;
use App\Http\Resources\AccommodationResource;
use App\Models\Accommodation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\AvailabilityRequest;
use App\Services\GeocodingService;

class AccommodationController extends Controller
{

    public function __construct(
        protected GeocodingService $geocodingService
    ) {}
    /**
     * Display a listing of the resource.
     */
    /**
     * GET|HEAD /api/accommodations
     * listar todos los alojamientos (con filtros opcionales)
     * 
     * Query params opcionales:
     * - city: filtrar por ciudad
     * - status: filtrar por estado
     * - min_price: precio mínimo
     * - max_price: precio máximo
     * - guests: número de huéspedes
     */
    public function index(Request $request)
    {
        // 1. Empezamos con una consulta base
        $query = Accommodation::query();
        $query->whereNull('deleted_at');
        
        // 2. Aplicar filtros según el rol del usuario
        $user = $request->user();
        
        if ($user->role === 'owner') {
            // Los owners solo ven sus propias propiedades
            $query->where('owner_id', $user->id);
        } elseif ($user->role === 'guest') {
            // Los guests solo ven propiedades publicadas
            $query->where('status', 'published');
        }
        
        // 3. Filtros opcionales por query params
        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }
        
        if ($request->has('status') && $user->role !== 'guest') {
            // Solo admin/owner pueden filtrar por estado
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
        
        // 4. Eager Loading para evitar N+1 queries
        $query->with(['owner', 'cancellationPolicy', 'media']);
        
        // 5. Paginación (10 por página)
        $accommodations = $query->paginate(10);
        
        // 6. Devolver colección de recursos
        return AccommodationResource::collection($accommodations);
    }
    /**
     * POST /api/accommodations
     * CREAR un nuevo alojamiento
     * Solo owners y admin pueden crear
     */
    public function store(StoreAccommodationRequest $request)
    {
        // 1. Validación automática
        $validated = $request->validated();
        
        // 2. Asignar owner_id según el rol
        $user = $request->user();
        
        if ($user->role === 'owner') {
            $validated['owner_id'] = $user->id;
        } elseif ($user->role === 'admin') {
            $validated['owner_id'] = $request->owner_id ?? $user->id;
        } else {
            return response()->json(['message' => 'No tienes permiso'], 403);
        }
        
        // 3. Generar slug
        $validated['slug'] = Str::slug($validated['title']);
        
        
        // 4. Geocodificar si no vienen coordenadas
        if (empty($validated['latitude']) || empty($validated['longitude'])) {
            $address = trim($validated['address']) . ', ' . trim($validated['city']) . ', ' . trim($validated['country']);
            $coordinates = $this->geocodingService->geocodeAddress($address);
            
            if ($coordinates) {
                $validated['latitude'] = $coordinates['lat'];
                $validated['longitude'] = $coordinates['lon'];
            }
        }
        
        // 5. Crear alojamiento
        $accommodation = Accommodation::create($validated);
        $accommodation->load(['owner', 'cancellationPolicy']);
        
        return new AccommodationResource($accommodation);
    }

    /**
     * GET|HEAD /api/accommodations/{id}
     * MOSTRAR un alojamiento específico
     */
    public function show(Accommodation $accommodation)
    {
        // 1. Verificar permisos (Policy)
        if (!Gate::allows('view', $accommodation)) {
            return response()->json([
                'message' => 'No tienes permiso para ver este alojamiento'
            ], 403);
        }
        
        // 2. Cargar relaciones
        $accommodation->load(['owner', 'cancellationPolicy', 'media']);
        
        // 3. Devolver recurso
        return new AccommodationResource($accommodation);
    }

    /**
     * PUT|PATCH /api/accommodations/{id}
     * actualizar un alojamiento
     */
    public function update(UpdateAccommodationRequest $request, Accommodation $accommodation)
    {
        if (!Gate::allows('update', $accommodation)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        
        $validated = $request->validated();
        
        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }
        
        // Geocodificar si cambió la dirección y no hay coordenadas
        $needsGeocoding = (empty($validated['latitude']) && empty($validated['longitude'])) 
            || (isset($validated['address']) || isset($validated['city']) || isset($validated['country']));
        
        if ($needsGeocoding) {
            $addressParts = [
                $validated['address'] ?? $accommodation->address,
                $validated['city'] ?? $accommodation->city,
                $validated['country'] ?? $accommodation->country
            ];
            
            $address = implode(', ', array_filter($addressParts));
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


    /**
     * delete /api/accommodations/{id}
     * eliminar un alojamiento (soft delete)
     */
    public function destroy(Accommodation $accommodation)
    {
        // 1. Verificar permisos (Policy)
        if (!Gate::allows('delete', $accommodation)) {
            return response()->json([
                'message' => 'No tienes permiso para eliminar este alojamiento'
            ], 403);
        }
        
        // 2. Soft delete
        $accommodation->delete();
        
        // 3. Devolver respuesta vacía (204 No Content)
        return response()->json(null, 204);
    }

    /**
     * GET /api/accommodations/{id}/availability
     * Ver disponibilidad de un alojamiento en un rango de fechas
     */
    public function availability(AvailabilityRequest $request, Accommodation $accommodation)
    {
        if (!Gate::allows('view', $accommodation)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        
        $unavailableDates = $accommodation->availabilityCalendars()
            ->whereBetween('date', [$request->check_in, $request->check_out])
            ->whereIn('status', ['booked', 'blocked', 'maintenance'])
            ->get();
        
        if ($unavailableDates->isEmpty()) {
            return response()->json(['available' => true]);
        }
        
        return response()->json([
            'available' => false,
            'conflicts' => $unavailableDates->pluck('date')
        ]);
    }

    /**
 * GET /api/accommodations/public
 * Listado público de alojamientos (para home y visitantes)
 */
    public function publicList(Request $request)
    {
        $query = Accommodation::where('status', 'published')
                            ->whereNull('deleted_at')
                            ->with(['owner', 'media']);
        
         // Filtro por ciudad con unaccent (quita tildes) e ILIKE (ignora mayúsculas) ojo, en PostgreSQL, en MySQL se usaría LIKE normal
        if ($request->has('city')) {
            $city = $request->city;
            $query->whereRaw('unaccent(city) ILIKE unaccent(?)', ['%' . $city . '%']);
    }
        
        if ($request->has('guests')) {
            $query->where('max_guests', '>=', $request->guests);
        }
        
        if ($request->has('check_in') && $request->has('check_out')) {
            // Filtrar por disponibilidad (más complejo)
        }
        
        $accommodations = $query->paginate(6);
        
        return AccommodationResource::collection($accommodations);
    }

/**
 * GET /api/accommodations/{id}/public
 * Detalle público de un alojamiento (para visitantes)
 */
    public function publicShow($id)
    {
        $accommodation = Accommodation::with([
            'owner', 
            'media',
            'reviews' => function($query) {
                $query->where('status', 'published')
                    ->with('guest')
                    ->latest()
                    ->limit(10);
            }
        ])->findOrFail($id);
        
        if ($accommodation->status !== 'published') {
            return response()->json(['message' => 'Alojamiento no disponible'], 404);
        }
        
        return new AccommodationResource($accommodation);
    }

}
