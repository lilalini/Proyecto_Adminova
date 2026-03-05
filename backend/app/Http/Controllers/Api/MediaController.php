<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MediaController extends Controller
{
    /**
     * Listado paginado de media, con filtros opcionales
     */
    public function index(Request $request)
    {
        $query = Media::query();

        if ($request->has('model_type') && $request->has('model_id')) {
            $query->where('model_type', $request->model_type)
                  ->where('model_id', $request->model_id);
        }

        if ($request->has('collection_name')) {
            $query->where('collection_name', $request->collection_name);
        }

        $media = $query->orderBy('order')->paginate(20);
        return MediaResource::collection($media);
    }

    /**
     * Crear nuevo media
     */
    public function store(StoreMediaRequest $request)
    {
        if (!Gate::allows('create', Media::class)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $media = Media::create($request->validated());
        return new MediaResource($media);
    }

    /**
     * Mostrar un media específico, incluyendo borrados
     */
    public function show($id)
    {
        $media = Media::withTrashed()->findOrFail($id);
        return new MediaResource($media);
    }

    /**
     * Actualizar media
     */
    public function update(UpdateMediaRequest $request, $id)
{
    // Buscar media incluyendo los borrados si hace falta
    $media = Media::withTrashed()->findOrFail($id);

    // Permiso
    if (!Gate::allows('update', $media)) {
        return response()->json(['message' => 'No autorizado'], 403);
    }

    // Actualizar solo con los datos validados
    $media->update($request->validated());

    // Devolver Resource correctamente
    return new MediaResource($media);
}

    /**
     * Eliminar media
     */
    public function destroy(Media $media)
    {
        if (!Gate::allows('delete', $media)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $media->delete();
        return response()->json(null, 204);
    }

    /**
     * Marcar un media como principal en su colección
     */
    public function setMain(Media $media)
    {
        if (!Gate::allows('update', $media)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        Media::where('model_type', $media->model_type)
            ->where('model_id', $media->model_id)
            ->where('collection_name', $media->collection_name)
            ->update(['is_main' => false]);

        $media->update(['is_main' => true]);

        return new MediaResource($media);
    }

    /**
     * Reordenar colección de media
     */
    public function reorder(Request $request)
    {
        if (!Gate::allows('update', Media::class)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'media_ids' => 'required|array',
            'media_ids.*' => 'exists:media,id',
        ]);

        foreach ($request->media_ids as $index => $id) {
            Media::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['message' => 'Orden actualizado']);
    }
}