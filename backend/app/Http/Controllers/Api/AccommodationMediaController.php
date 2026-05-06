<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccommodationResource;
use App\Models\Accommodation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AccommodationMediaController extends Controller
{
    public function store(Request $request, Accommodation $accommodation)
    {
        Gate::authorize('create', [$accommodation, Accommodation::class]);

        $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,avif|max:5120',
        ]);

        $file = $request->file('image');
        $isFirst = $accommodation->getMedia('gallery')->isEmpty();

        $media = $accommodation
            ->addMedia($file)
            ->usingFileName(time() . '_' . $file->getClientOriginalName())
            ->withCustomProperties(['is_main' => $isFirst]) // primera imagen → portada automática
            ->toMediaCollection('gallery', 'public');

        return response()->json([
            'id' => $media->id,
            'url' => $media->getUrl(),
            'thumb' => $media->getUrl('thumb'),
            'is_main' => (bool) $media->getCustomProperty('is_main', false),
        ], 201);
    }

    public function destroy(Accommodation $accommodation, $mediaId)
    {
        Gate::authorize('delete', [$accommodation, Accommodation::class]);

        $media = $accommodation->media()->findOrFail($mediaId);
        $wasMain = (bool) $media->getCustomProperty('is_main', false);
        $media->delete();

        // Si era la principal, asignar la siguiente automáticamente
        if ($wasMain) {
            $next = $accommodation->getMedia('gallery')->first();
            $next?->setCustomProperty('is_main', true)->save();
        }

        $accommodation->load('media');
        return new AccommodationResource($accommodation);
    }

    /**
     * PATCH /api/accommodations/{accommodation}/media/{mediaId}/set-main
     * Cambiar la imagen principal
     */
    public function setMain(Accommodation $accommodation, $mediaId)
    {
        Gate::authorize('create', [$accommodation, Accommodation::class]); // reutilizamos permiso de gestión

        // Quitar is_main de todas
        foreach ($accommodation->getMedia('gallery') as $m) {
            $m->setCustomProperty('is_main', false)->save();
        }

        // Marcar la elegida
        $media = $accommodation->media()->findOrFail($mediaId);
        $media->setCustomProperty('is_main', true)->save();

        return response()->json([
            'id' => $media->id,
            'url' => $media->getUrl(),
            'is_main' => true,
        ]);
    }
}