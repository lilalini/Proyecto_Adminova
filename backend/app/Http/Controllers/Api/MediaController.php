<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Media::query();

        // Admin ve todo, owner solo su media
        if ($user->role === 'owner') {
            $query->where('model_type', 'App\Models\Accommodation')
                  ->whereHas('model', fn($q) => $q->where('owner_id', $user->id));
        }

        if ($request->has('model_type') && $request->has('model_id')) {
            $query->where('model_type', $request->model_type)
                  ->where('model_id', $request->model_id);
        }

        if ($request->has('collection_name')) {
            $query->where('collection_name', $request->collection_name);
        }

        return MediaResource::collection($query->orderBy('order_column')->paginate(20));
    }

    public function store(StoreMediaRequest $request)
    {
        $this->authorize('create', Media::class);
        $media = Media::create($request->validated());
        return new MediaResource($media);
    }

    public function show($id)
    {
        $media = Media::withTrashed()->findOrFail($id);
        return new MediaResource($media);
    }

    public function update(UpdateMediaRequest $request, $id)
    {
        $media = Media::withTrashed()->findOrFail($id);
        $this->authorize('update', $media);
        $media->update($request->validated());
        return new MediaResource($media);
    }

    public function destroy(Media $media)
    {
        //$this->authorize('delete', $media);
        $media->delete();
        return response()->json(null, 204);
    }

    public function setMain(Media $media)
    {
        $this->authorize('update', $media);
        $media->setAsMain(); // usando método del modelo
        return new MediaResource($media->fresh());
    }

    public function reorder(Request $request)
    {
        // Acción solo para admin
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'media_ids' => 'required|array',
            'media_ids.*' => 'exists:media,id',
        ]);

        foreach ($request->media_ids as $index => $id) {
            Media::where('id', $id)->update(['order_column' => $index]);
        }

        return response()->json(['message' => 'Orden actualizado']);
    }
}