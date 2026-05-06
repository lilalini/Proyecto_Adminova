<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $notifications = Notification::where('notifiable_id', $user->id)
            ->where('notifiable_type', 'LIKE', '%User%')  // ← Filtro flexible
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return NotificationResource::collection($notifications);
    }
    public function show(Notification $notification)
    {
        $this->authorize('view', $notification);
        return new NotificationResource($notification);
    }

    public function destroy(Notification $notification)
    {
        $this->authorize('delete', $notification);
        $notification->delete();
        return response()->json(null, 204);
    }

    public function markAsRead(Request $request, Notification $notification)
    {
        $this->authorize('markAsRead', $notification);
        $notification->markAsRead();
        return new NotificationResource($notification->fresh());
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();

        Notification::where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->where('is_read', false)
            ->each(fn($n) => $n->markAsRead());

        return response()->json(['message' => 'Todas las notificaciones marcadas como leídas']);
    }
}