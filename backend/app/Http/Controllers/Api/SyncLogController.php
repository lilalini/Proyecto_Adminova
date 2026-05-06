<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SyncLogResource;
use App\Models\SyncLog;
use Illuminate\Http\Request;

class SyncLogController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', SyncLog::class);

        $query = SyncLog::with(['accommodation', 'channel', 'createdBy']);

        if ($request->has('accommodation_id')) {
            $query->where('accommodation_id', $request->accommodation_id);
        }

        if ($request->has('channel_id')) {
            $query->where('channel_id', $request->channel_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('sync_type')) {
            $query->where('sync_type', $request->sync_type);
        }

        if ($request->has('from')) {
            $query->where('created_at', '>=', $request->from);
        }

        if ($request->has('to')) {
            $query->where('created_at', '<=', $request->to);
        }

        return SyncLogResource::collection($query->orderBy('created_at', 'desc')->paginate(15));
    }

    public function show(SyncLog $syncLog)
    {
        $this->authorize('view', $syncLog);
        $syncLog->load(['accommodation', 'channel', 'createdBy']);
        return new SyncLogResource($syncLog);
    }

    public function destroy(SyncLog $syncLog)
    {
        $this->authorize('delete', $syncLog);
        $syncLog->delete();
        return response()->json(null, 204);
    }
}