<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SyncLogResource;
use App\Models\SyncLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SyncLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!Gate::allows('viewAny', SyncLog::class)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

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

        $logs = $query->orderBy('created_at', 'desc')->paginate(15);
        return SyncLogResource::collection($logs);
    }

    public function show(SyncLog $syncLog)
    {
        if (!Gate::allows('view', $syncLog)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $syncLog->load(['accommodation', 'channel', 'createdBy']);
        return new SyncLogResource($syncLog);
    }

    public function destroy(SyncLog $syncLog)
    {
        if (!Gate::allows('delete', $syncLog)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $syncLog->delete();
        return response()->json(null, 204);
    }
}
