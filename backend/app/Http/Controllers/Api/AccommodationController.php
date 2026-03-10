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

class AccommodationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * GET|HEAD /api/accommodations
     * LISTAR todos los alojamientos (con filtros opcionales)
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
        
        // 2. Aplicar filtros según el rol del usuario
        $user = $request->user();
        
        if ($user->role === 'owner') {
            // Los owners solo ven sus propias propiedades
            $query->where('owner_id', $user->id);
        } elseif ($user->role === 'guest') {
            // Los guests solo ven propiedades publicadas
            $query->where('status', 'published');
        }
        // Los admin ven todo (sin filtro adicional)
        
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
        // 1. Validación automática por StoreAccommodationRequest
        $validated = $request->validated();
        
        // 2. Asignar owner_id según el rol
        $user = $request->user();
        
        if ($user->role === 'owner') {
            // Si es owner, el alojamiento es suyo
            $validated['owner_id'] = $user->id;
        } elseif ($user->role === 'admin') {
            // Si es admin, puede asignar a cualquier owner
            $validated['owner_id'] = $request->owner_id ?? $user->id;
        } else {
            // Guest no puede crear alojamientos
            return response()->json([
                'message' => 'No tienes permiso para crear alojamientos'
            ], 403);
        }
        
        // 3. Generar slug a partir del título
        $validated['slug'] = Str::slug($validated['title']);
        
        // 4. Crear el alojamiento
        $accommodation = Accommodation::create($validated);
        
        // 5. Cargar relaciones para la respuesta
        $accommodation->load(['owner', 'cancellationPolicy']);
        
        // 6. Devolver recurso creado
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
     * ACTUALIZAR un alojamiento
     */
    public function update(UpdateAccommodationRequest $request, Accommodation $accommodation)
    {
        // 1. Verificar permisos (Policy)
        if (!Gate::allows('update', $accommodation)) {
            return response()->json([
                'message' => 'No tienes permiso para actualizar este alojamiento'
            ], 403);
        }
        
        // 2. Validación automática
        $validated = $request->validated();
        
        // 3. Si cambia el título, regenerar slug
        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }
        
        // 4. Actualizar
        $accommodation->update($validated);
        
        // 5. Recargar relaciones
        $accommodation->load(['owner', 'cancellationPolicy', 'media']);
        
        // 6. Devolver recurso actualizado
        return new AccommodationResource($accommodation);
    }

    /**
     * DELETE /api/accommodations/{id}
     * ELIMINAR un alojamiento (soft delete)
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
     * Ver disponibilidad de un alojamiento (método extra)
     */
    public function availability(Request $request, Accommodation $accommodation)
    {
        // Verificar permisos
        if (!Gate::allows('view', $accommodation)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        
        // Validar fechas
        $request->validate([
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);
        
        // Consultar disponibilidad en availability_calendars
        $unavailableDates = $accommodation->availabilityCalendars()
            ->whereBetween('date', [$request->check_in, $request->check_out])
            ->whereIn('status', ['booked', 'blocked', 'maintenance'])
            ->get();
        
        if ($unavailableDates->isEmpty()) {
            return response()->json([
                'available' => true,
                'message' => 'El alojamiento está disponible'
            ]);
        }
        
        return response()->json([
            'available' => false,
            'message' => 'El alojamiento no está disponible en las fechas seleccionadas',
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
                          ->with(['owner', 'media']);
    
    // Filtros básicos para visitantes
    if ($request->has('city')) {
        $query->where('city', 'like', '%' . $request->city . '%');
    }
    
    if ($request->has('guests')) {
        $query->where('max_guests', '>=', $request->guests);
    }
    
    if ($request->has('check_in') && $request->has('check_out')) {
        // Filtrar por disponibilidad (más complejo)
    }
    
    $accommodations = $query->paginate(12);
    
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
