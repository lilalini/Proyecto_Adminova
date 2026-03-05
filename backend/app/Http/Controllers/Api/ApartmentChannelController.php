<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApartmentChannelRequest;
use App\Http\Requests\UpdateApartmentChannelRequest;
use App\Http\Resources\ApartmentChannelResource;
use App\Models\ApartmentChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ApartmentChannelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ApartmentChannel::with(['accommodation', 'distributionChannel']);

        if ($request->has('accommodation_id')) {
            $query->where('accommodation_id', $request->accommodation_id);
        }

        if ($request->has('channel_id')) {
            $query->where('distribution_channel_id', $request->channel_id);
        }

        if ($request->has('connection_status')) {
            $query->where('connection_status', $request->connection_status);
        }

        $channels = $query->paginate(15);
        return ApartmentChannelResource::collection($channels);
    }

    public function store(StoreApartmentChannelRequest $request)
    {
        if (!Gate::allows('create', ApartmentChannel::class)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Verificar unique (accommodation_id + distribution_channel_id)
        $exists = ApartmentChannel::where('accommodation_id', $request->accommodation_id)
            ->where('distribution_channel_id', $request->distribution_channel_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Esta combinación ya existe'
            ], 422);
        }

        $channel = ApartmentChannel::create($request->validated());
        $channel->load(['accommodation', 'distributionChannel']);

        return new ApartmentChannelResource($channel);
    }

    public function show(ApartmentChannel $apartmentChannel)
    {
        if (!Gate::allows('view', $apartmentChannel)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $apartmentChannel->load(['accommodation', 'distributionChannel']);
        return new ApartmentChannelResource($apartmentChannel);
    }

    public function update(UpdateApartmentChannelRequest $request, ApartmentChannel $apartmentChannel)
    {
        if (!Gate::allows('update', $apartmentChannel)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $apartmentChannel->update($request->validated());
        $apartmentChannel->load(['accommodation', 'distributionChannel']);

        return new ApartmentChannelResource($apartmentChannel);
    }

    public function destroy(ApartmentChannel $apartmentChannel)
    {
        if (!Gate::allows('delete', $apartmentChannel)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $apartmentChannel->delete();
        return response()->json(null, 204);
    }

    public function sync(Request $request, ApartmentChannel $apartmentChannel)
    {
        if (!Gate::allows('update', $apartmentChannel)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Aquí iría la lógica de sincronización con el canal
        $apartmentChannel->update([
            'last_sync_at' => now(),
            'last_sync_status' => 'success',
        ]);

        return response()->json(['message' => 'Sincronización iniciada']);
    }
}
