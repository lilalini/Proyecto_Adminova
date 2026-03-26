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

    // Validación estricta de la imagen
    $request->validate([
        'image' => 'required|file|mimes:jpeg,png,jpg,gif,avif|max:5120'
    ]);

    $file = $request->file('image');

    // Evitar errores de Spatie al sobrescribir nombres existentes
    $media = $accommodation
        ->addMedia($file)
        ->usingFileName(time().'_'.$file->getClientOriginalName())
        ->toMediaCollection('gallery', 'public');

    return response()->json([
        'url' => $media->getUrl(),
        'data' => $media
    ]);
}
    
    public function destroy(Accommodation $accommodation, $mediaId)
    {
        Gate::authorize('delete', [$accommodation, Accommodation::class]);
        
        $media = $accommodation->media()->findOrFail($mediaId);
        $media->delete();
        
        // Recargar y devolver alojamiento actualizado
        $accommodation->load('media');
        return new AccommodationResource($accommodation);
    }
}   