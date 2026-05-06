<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DistributionChannel;
use App\Http\Resources\DistributionChannelResource;
use Illuminate\Http\Request;

class DistributionChannelController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = DistributionChannel::query();

        if ($user->role !== 'admin') {
            // No admin solo ve canales activos sin api_config
            $query->active();
        }

        return DistributionChannelResource::collection($query->get());
    }

    public function store(Request $request)
    {
        $this->authorize('create', DistributionChannel::class);

        $validated = $request->validate([
            'channel_code' => 'required|string|unique:distribution_channels,channel_code',
            'name' => 'required|string|max:255',
            'channel_type' => 'required|in:OTA,direct,corporate,referral',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'api_config' => 'nullable|array',
            'is_active' => 'boolean',
            'sync_enabled' => 'boolean',
        ]);

        $channel = DistributionChannel::create($validated);
        return new DistributionChannelResource($channel);
    }

    public function show(DistributionChannel $distributionChannel)
    {
        $this->authorize('view', $distributionChannel);
        return new DistributionChannelResource($distributionChannel);
    }

    public function update(Request $request, DistributionChannel $distributionChannel)
    {
        $this->authorize('update', $distributionChannel);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'channel_type' => 'sometimes|in:OTA,direct,corporate,referral',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'api_config' => 'nullable|array',
            'is_active' => 'sometimes|boolean',
            'sync_enabled' => 'sometimes|boolean',
        ]);

        $distributionChannel->update($validated);
        return new DistributionChannelResource($distributionChannel);
    }

    public function destroy(DistributionChannel $distributionChannel)
    {
        $this->authorize('delete', $distributionChannel);
        $distributionChannel->delete();
        return response()->json(null, 204);
    }
}