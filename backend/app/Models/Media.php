<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Facades\Storage;

class Media extends SpatieMedia implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;
    // Atributos que se incluyen automáticamente en las respuestas JSON
    protected $appends = ['url', 'thumbnail_url'];

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'model_type',        // Accommodation, User, Owner, Guest, CleaningTask
        'model_id',
        'collection_name',   // gallery, profile, documents, tasks, etc.
        'name',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'disk',              // public, s3, local
        'order',
        'is_main',
        'alt_text',
        'title',
        'description',
        'metadata',          // JSON con dimensiones, coordenadas GPS, etc.
    ];

    // Casts para tipos de datos
    protected $casts = [
        'file_size' => 'integer',
        'order' => 'integer',
        'is_main' => 'boolean',
        'metadata' => 'array',
    ];
  

    // ==================== scopes ====================
    /**
     * Filtra por colección (gallery, profile, documents, etc.)
     */
    public function scopeForCollection($query, $collectionName)
    {
        return $query->where('collection_name', $collectionName);
    }

    /**
     * Filtra la imagen principal (is_main = true)
     */
    public function scopeMainImage($query)
    {
        return $query->where('is_main', true);
    }

    /**
     * Filtra solo imágenes
     */
    public function scopeImages($query)
    {
        return $query->whereIn('mime_type', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
    }

    /**
     * Filtra solo documentos (PDF, Word, etc.)
     */
    public function scopeDocuments($query)
    {
        return $query->whereIn('mime_type', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
    }

    // ==================== metodos auxiliares ====================
    /**
     * Determina si el archivo es una imagen
     */
    public function isImage()
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Determina si el archivo es un PDF
     */
    public function isPdf()
    {
        return $this->mime_type === 'application/pdf';
    }

    /**
     * Obtiene la URL completa del archivo (usando el método de Spatie)
     */
    public function getUrlAttribute()
    {
        return $this->getUrl(); // Método nativo de Spatie
    }

    /**
     * Obtiene la URL del thumbnail (conversión 'thumb' si existe)
     */
    public function getThumbnailUrlAttribute()
    {
        return $this->hasGeneratedConversion('thumb') 
            ? $this->getUrl('thumb') 
            : $this->getUrl();
    }

    /**
     * Obtiene el tamaño del archivo en formato legible (B, KB, MB, GB)
     */
    public function getHumanReadableSize()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Establece este archivo como el principal de su colección
     * Quita la marca 'is_main' de los demás en la misma colección
     */
    public function setAsMain()
    {
        static::where('model_type', $this->model_type)
            ->where('model_id', $this->model_id)
            ->where('collection_name', $this->collection_name)
            ->where('id', '!=', $this->id)
            ->update(['is_main' => false]);

        $this->update(['is_main' => true]);
    }

    // ==================== eventos ====================
    /**
     * Boot del modelo
     * Elimina el archivo físico cuando se elimina el registro de la BD
     */
    protected static function booted()
    {
        static::deleted(function ($media) {
            if ($media->file_path && Storage::disk($media->disk)->exists($media->file_path)) {
                Storage::disk($media->disk)->delete($media->file_path);
            }
        });
    }
}