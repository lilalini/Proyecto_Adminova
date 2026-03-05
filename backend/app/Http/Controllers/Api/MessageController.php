<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index(Request $request)
    {
        $user = $request->user();
        
        $messages = Message::with(['sender', 'receiver', 'accommodation', 'booking'])
            ->where(function($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->where('sender_type', get_class($user))
                  ->orWhere(function($q2) use ($user) {
                      $q2->where('receiver_id', $user->id)
                         ->where('receiver_type', get_class($user));
                  });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return MessageResource::collection($messages);
    }

    public function store(StoreMessageRequest $request)
    {
        $data = $request->validated();
        $data['sender_type'] = get_class($request->user());
        $data['sender_id'] = $request->user()->id;
        $data['sent_at'] = now();

        $message = Message::create($data);
        $message->load(['sender', 'receiver', 'accommodation', 'booking']);

        return new MessageResource($message);
    }

    public function show(Message $message)
    {
        if (!Gate::allows('view', $message)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Marcar como leído si es el receptor
        if ($message->receiver_id === request()->user()->id && 
            $message->receiver_type === get_class(request()->user()) && 
            !$message->is_read) {
            $message->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        $message->load(['sender', 'receiver', 'accommodation', 'booking', 'parent']);
        return new MessageResource($message);
    }

    public function destroy(Message $message)
    {
        if (!Gate::allows('delete', $message)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $message->delete();
        return response()->json(null, 204);
    }

    public function markAsRead(Request $request, Message $message)
    {
        if (!Gate::allows('view', $message)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $message->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return new MessageResource($message);
    }

    public function conversations(Request $request)
    {
        $user = $request->user();
        
        // Obtener últimas conversaciones (agrupadas por otro usuario)
        $messages = Message::where(function($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->where('sender_type', get_class($user))
                  ->orWhere(function($q2) use ($user) {
                      $q2->where('receiver_id', $user->id)
                         ->where('receiver_type', get_class($user));
                  });
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($message) use ($user) {
                if ($message->sender_id === $user->id && $message->sender_type === get_class($user)) {
                    return $message->receiver_type . '_' . $message->receiver_id;
                }
                return $message->sender_type . '_' . $message->sender_id;
            })
            ->map(function($group) {
                return $group->first();
            })
            ->values();

        return MessageResource::collection($messages);
    }
}
