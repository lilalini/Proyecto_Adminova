<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $notifications = Notification::where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return NotificationResource::collection($notifications);
    }

    public function show(Notification $notification)
    {
        if (!Gate::allows('view', $notification)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        return new NotificationResource($notification);
    }

    public function destroy(Notification $notification)
    {
        if (!Gate::allows('delete', $notification)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $notification->delete();
        return response()->json(null, 204);
    }

    public function markAsRead(Request $request, Notification $notification)
    {
        if (!Gate::allows('markAsRead', $notification)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return new NotificationResource($notification);
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        
        Notification::where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['message' => 'Todas las notificaciones marcadas como leídas']);
    }
}