<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApartmentChannelRequest;
use App\Http\Requests\UpdateApartmentChannelRequest;
use App\Http\Resources\ApartmentChannelResource;
use App\Models\ApartmentChannel;
use Illuminate\Http\Request;

class ApartmentChannelController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = ApartmentChannel::with(['accommodation', 'distributionChannel']);

        // Filtro por rol
        if ($user->role === 'owner') {
            $query->whereHas('accommodation', fn($q) => $q->where('owner_id', $user->id));
        } elseif ($user->role === 'guest') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($request->has('accommodation_id')) {
            $query->where('accommodation_id', $request->accommodation_id);
        }

        if ($request->has('channel_id')) {
            $query->where('distribution_channel_id', $request->channel_id);
        }

        if ($request->has('connection_status')) {
            $query->where('connection_status', $request->connection_status);
        }

        return ApartmentChannelResource::collection($query->paginate(15));
    }

    public function store(StoreApartmentChannelRequest $request)
    {
        $this->authorize('create', ApartmentChannel::class);

        $exists = ApartmentChannel::where('accommodation_id', $request->accommodation_id)
            ->where('distribution_channel_id', $request->distribution_channel_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Esta combinación de alojamiento y canal ya existe'
            ], 422);
        }

        $channel = ApartmentChannel::create($request->validated());
        $channel->load(['accommodation', 'distributionChannel']);

        return new ApartmentChannelResource($channel);
    }

    public function show(ApartmentChannel $apartmentChannel)
    {
        $this->authorize('view', $apartmentChannel);
        $apartmentChannel->load(['accommodation', 'distributionChannel']);
        return new ApartmentChannelResource($apartmentChannel);
    }

    public function update(UpdateApartmentChannelRequest $request, ApartmentChannel $apartmentChannel)
    {
        $this->authorize('update', $apartmentChannel);
        $apartmentChannel->update($request->validated());
        $apartmentChannel->load(['accommodation', 'distributionChannel']);
        return new ApartmentChannelResource($apartmentChannel);
    }

    public function destroy(ApartmentChannel $apartmentChannel)
    {
        $this->authorize('delete', $apartmentChannel);
        $apartmentChannel->delete();
        return response()->json(null, 204);
    }

    public function sync(Request $request, ApartmentChannel $apartmentChannel)
    {
        $this->authorize('update', $apartmentChannel);

        // Marcar inicio de sincronización
        $apartmentChannel->markAsSynced('pending', 'Sincronización iniciada');

        try {
            // Sincronizar precio si está habilitado
            if ($apartmentChannel->sync_price) {
                $basePrice = $apartmentChannel->accommodation->base_price;

                if ($apartmentChannel->price_adjustment_type === 'percentage') {
                    $finalPrice = $basePrice * (1 + $apartmentChannel->price_adjustment_value / 100);
                } elseif ($apartmentChannel->price_adjustment_type === 'fixed') {
                    $finalPrice = $basePrice + $apartmentChannel->price_adjustment_value;
                } else {
                    $finalPrice = $basePrice;
                }

                // TODO: llamar a la API del canal externo con $finalPrice
            }

            // Sincronizar disponibilidad si está habilitado
            if ($apartmentChannel->sync_availability) {
                // TODO: enviar calendario de disponibilidad al canal externo
            }

            // Sincronizar contenido si está habilitado
            if ($apartmentChannel->sync_content) {
                // TODO: enviar fotos y descripción al canal externo
            }

            $apartmentChannel->markAsSynced('success', 'Sincronización completada correctamente');

            return response()->json([
                'message' => 'Sincronización completada',
                'last_sync_at' => $apartmentChannel->last_sync_at,
                'last_sync_status' => $apartmentChannel->last_sync_status,
            ]);

        } catch (\Exception $e) {
            $apartmentChannel->markAsSynced('error', $e->getMessage());

            return response()->json([
                'message' => 'Error en la sincronización',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}