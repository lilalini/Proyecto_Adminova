<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCleaningTaskRequest;
use App\Http\Requests\UpdateCleaningTaskRequest;
use App\Http\Resources\CleaningTaskResource;
use App\Models\CleaningTask;
use Illuminate\Http\Request;

class CleaningTaskController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', CleaningTask::class);

        $user = $request->user();
        $query = CleaningTask::with(['accommodation', 'booking', 'assignedTo', 'createdBy']);

        // Staff solo ve sus tareas
        if ($user->role === 'staff') {
            $query->where('assigned_to_user_id', $user->id);
        }

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

        return CleaningTaskResource::collection($query->orderBy('scheduled_date')->paginate(15));
    }

    public function store(StoreCleaningTaskRequest $request)
    {
        $this->authorize('create', CleaningTask::class);

        $data = $request->validated();
        $data['created_by_user_id'] = $request->user()->id;
        $data['status'] = 'pending';

        $task = CleaningTask::create($data);
        $task->load(['accommodation', 'booking', 'assignedTo', 'createdBy']);

        return new CleaningTaskResource($task);
    }

    public function show(CleaningTask $cleaningTask)
    {
        $this->authorize('view', $cleaningTask);
        $cleaningTask->load(['accommodation', 'booking', 'assignedTo', 'createdBy', 'verifiedBy']);
        return new CleaningTaskResource($cleaningTask);
    }

    public function update(UpdateCleaningTaskRequest $request, CleaningTask $cleaningTask)
    {
        $this->authorize('update', $cleaningTask);
        $cleaningTask->update($request->validated());
        $cleaningTask->load(['accommodation', 'booking', 'assignedTo', 'createdBy']);
        return new CleaningTaskResource($cleaningTask);
    }

    public function destroy(CleaningTask $cleaningTask)
    {
        $this->authorize('delete', $cleaningTask);
        $cleaningTask->delete();
        return response()->json(null, 204);
    }

    public function verify(Request $request, CleaningTask $cleaningTask)
    {
        $this->authorize('verify', $cleaningTask);

        if ($cleaningTask->status !== 'completed') {
            return response()->json([
                'message' => 'Solo se pueden verificar tareas completadas'
            ], 422);
        }

        $cleaningTask->verify($request->user()->id);
        return new CleaningTaskResource($cleaningTask->fresh());
    }

    public function assign(Request $request, CleaningTask $cleaningTask)
    {
        $this->authorize('assign', $cleaningTask);

        $request->validate([
            'assigned_to_user_id' => 'required|exists:users,id',
        ]);

        $cleaningTask->assignTo($request->assigned_to_user_id);
        $cleaningTask->load(['assignedTo']);

        return new CleaningTaskResource($cleaningTask);
    }
}