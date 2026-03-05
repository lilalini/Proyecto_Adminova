<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Accommodation;
use App\Models\Owner;
use App\Models\Media;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fotos de accommodations (5-10 por propiedad)
        $accommodations = Accommodation::all();
        foreach ($accommodations as $index => $accommodation) {
            $numPhotos = rand(5, 10);
            
            for ($i = 0; $i < $numPhotos; $i++) {
                Media::create([
                    'model_type' => Accommodation::class,
                    'model_id' => $accommodation->id,
                    'collection_name' => 'gallery',
                    'name' => 'photo_' . ($i + 1),
                    'file_name' => 'apt_' . $accommodation->id . '_' . ($i + 1) . '.jpg',
                    'file_path' => 'uploads/accommodations/' . $accommodation->id . '/photo_' . ($i + 1) . '.jpg',
                    'file_size' => rand(200000, 800000),
                    'mime_type' => 'image/jpeg',
                    'disk' => 'public',
                    'order' => $i + 1,
                    'is_main' => $i === 0, // primera es principal
                    'alt_text' => $accommodation->title . ' - foto ' . ($i + 1),
                    'metadata' => json_encode([
                        'width' => 1920,
                        'height' => 1080,
                        'orientation' => 'landscape'
                    ]),
                ]);
            }
        }
        
        // Fotos de perfil de owners (30%)
        $owners = Owner::all();
        foreach ($owners->random($owners->count() * 0.3) as $owner) {
            Media::create([
                'model_type' => Owner::class,
                'model_id' => $owner->id,
                'collection_name' => 'profile',
                'name' => 'profile',
                'file_name' => 'owner_' . $owner->id . '_profile.jpg',
                'file_path' => 'uploads/owners/' . $owner->id . '/profile.jpg',
                'file_size' => rand(50000, 200000),
                'mime_type' => 'image/jpeg',
                'disk' => 'public',
                'order' => 1,
                'is_main' => true,
                'alt_text' => $owner->first_name . ' ' . $owner->last_name,
                'metadata' => json_encode([
                    'width' => 400,
                    'height' => 400,
                    'orientation' => 'square'
                ]),
            ]);
        }
        
        // Foto específica para propiedad de prueba
        $testAccommodation = Accommodation::where('title', 'Ático de Lujo Centro')->first();
        if ($testAccommodation) {
            Media::updateOrCreate(
                [
                    'model_type' => Accommodation::class,
                    'model_id' => $testAccommodation->id,
                    'is_main' => true,
                ],
                [
                    'name' => 'main_photo',
                    'file_name' => 'atico_lujo_centro.jpg',
                    'file_path' => 'uploads/accommodations/test/atico_lujo_centro.jpg',
                    'file_size' => 350000,
                    'mime_type' => 'image/jpeg',
                    'alt_text' => 'Ático de lujo en centro Madrid',
                ]
            );
        }
    }
}
