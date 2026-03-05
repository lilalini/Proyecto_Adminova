<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCleaningTaskRequest;
use App\Http\Requests\UpdateCleaningTaskRequest;
use App\Http\Resources\CleaningTaskResource;
use App\Models\CleaningTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CleaningTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index(Request $request)
    {
        if (!Gate::allows('viewAny', CleaningTask::class)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $query = CleaningTask::with(['accommodation', 'booking', 'assignedTo', 'createdBy']);

        if ($request->has('accommodation_id')) {
            $query->where('accommodation_id', $request->accommodation_id);
        }

        if ($request->has('assigned_to')) {
            $query->where('assigned_to_user_id', $request->assigned_to);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('from')) {
            $query->where('scheduled_date', '>=', $request->from);
        }

        if ($request->has('to')) {
            $query->where('scheduled_date', '<=', $request->to);
        }

        $tasks = $query->orderBy('scheduled_date')->paginate(15);
        return CleaningTaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCleaningTaskRequest $request)
    {
        if (!Gate::allows('create', CleaningTask::class)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $data = $request->validated();
        $data['created_by_user_id'] = $request->user()->id;
        $data['status'] = 'pending';

        $task = CleaningTask::create($data);
        $task->load(['accommodation', 'booking', 'assignedTo', 'createdBy']);

        return new CleaningTaskResource($task);
    }

    /**
     * Display the specified resource.
     */
    public function show(CleaningTask $cleaningTask)
    {
        if (!Gate::allows('view', $cleaningTask)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $cleaningTask->load(['accommodation', 'booking', 'assignedTo', 'createdBy', 'verifiedBy']);
        return new CleaningTaskResource($cleaningTask);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCleaningTaskRequest $request, CleaningTask $cleaningTask)
    {
        if (!Gate::allows('update', $cleaningTask)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $cleaningTask->update($request->validated());
        $cleaningTask->load(['accommodation', 'booking', 'assignedTo', 'createdBy']);

        return new CleaningTaskResource($cleaningTask);
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(CleaningTask $cleaningTask)
    {
        if (!Gate::allows('delete', $cleaningTask)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $cleaningTask->delete();
        return response()->json(null, 204);
    }

    public function verify(Request $request, CleaningTask $cleaningTask)
    {
        if (!Gate::allows('verify', $cleaningTask)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $cleaningTask->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by_user_id' => $request->user()->id,
        ]);

        return new CleaningTaskResource($cleaningTask);
    }

    public function assign(Request $request, CleaningTask $cleaningTask)
    {
        if (!Gate::allows('assign', $cleaningTask)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'assigned_to_user_id' => 'required|exists:users,id',
        ]);

        $cleaningTask->update([
            'assigned_to_user_id' => $request->assigned_to_user_id,
            'status' => 'pending',
        ]);

        // Recargar la relación
        $cleaningTask->load(['assignedTo']);

        return new CleaningTaskResource($cleaningTask);
    }
}
