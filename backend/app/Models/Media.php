<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    /** @use HasFactory<\Database\Factories\MediaFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'legacy_media';
    protected $appends = ['url', 'thumbnail_url'];

    protected $fillable = [
        'model_type', // Accommodation, User, Owner, Guest, CleaningTask
        'model_id',
        'collection_name', // gallery, profile, documents, tasks, etc.
        'name',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'disk', // public, s3, local
        'order',
        'is_main',
        'alt_text',
        'title',
        'description',
        'metadata', // JSON con dimensiones, coordenadas GPS, etc.
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'order' => 'integer',
            'is_main' => 'boolean',
            'metadata' => 'array',
        ];
    }

    // Relación polimórfica
    public function model()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeForCollection($query, $collectionName)
    {
        return $query->where('collection_name', $collectionName);
    }

    public function scopeMainImage($query)
    {
        return $query->where('is_main', true);
    }

    public function scopeImages($query)
    {
        return $query->whereIn('mime_type', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
    }

    public function scopeDocuments($query)
    {
        return $query->whereIn('mime_type', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    // Métodos
    public function isImage()
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isPdf()
    {
        return $this->mime_type === 'application/pdf';
    }

    public function getUrl()
    {
        return asset('storage/' . $this->file_path);
    }

    public function getThumbnailUrl()
    {
        if (!$this->isImage()) {
            //return asset('images/file-icon.png');
        }

        // Podríamos generar thumbnail aquí
        return $this->getUrl();
    }

    public function getUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->isImage() ? $this->getUrl() : null;
    }

    public function getHumanReadableSize()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function setAsMain()
    {
        // Quitar main de otras imágenes del mismo modelo y colección
        static::where('model_type', $this->model_type)
            ->where('model_id', $this->model_id)
            ->where('collection_name', $this->collection_name)
            ->where('id', '!=', $this->id)
            ->update(['is_main' => false]);

        $this->update(['is_main' => true]);
    }

    protected static function booted()
    {
        static::deleted(function ($media) {
            if ($media->file_path && Storage::disk($media->disk)->exists($media->file_path)) {
                Storage::disk($media->disk)->delete($media->file_path);
            }
        });
    }
}
