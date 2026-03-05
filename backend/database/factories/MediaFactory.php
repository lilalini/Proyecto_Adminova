<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Media;
use App\Models\Accommodation;
use App\Models\Owner;
use App\Models\Guest;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Media>
 */
class MediaFactory extends Factory
{
    protected $model = Media::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       $modelTypes = [Accommodation::class, Owner::class, Guest::class, User::class];
        $modelType = fake()->randomElement($modelTypes);
        $model = $modelType::inRandomOrder()->first() ?? $modelType::factory();
        
        $extensions = ['jpg', 'png', 'webp', 'pdf', 'mp4'];
        $extension = fake()->randomElement($extensions);
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'pdf' => 'application/pdf',
            'mp4' => 'video/mp4'
        ];
        
        $isImage = in_array($extension, ['jpg', 'png', 'webp']);
        
        return [
            'model_type' => $modelType,
            'model_id' => $model instanceof $modelType ? $model->id : $model->create()->id,
            'collection_name' => fake()->randomElement(['default', 'gallery', 'profile', 'documents']),
            'name' => fake()->word(),
            'file_name' => fake()->slug() . '.' . $extension,
            'file_path' => 'uploads/' . fake()->uuid() . '.' . $extension,
            'file_size' => fake()->numberBetween(50000, 5000000),
            'mime_type' => $mimeTypes[$extension],
            'disk' => 'public',
            'order' => fake()->numberBetween(0, 10),
            'is_main' => fake()->boolean(20),
            'alt_text' => $isImage ? fake()->sentence(4) : null,
            'title' => fake()->optional(0.5)->sentence(3),
            'description' => fake()->optional(0.3)->paragraph(),
            'metadata' => $isImage ? json_encode([
                'width' => fake()->numberBetween(800, 4000),
                'height' => fake()->numberBetween(600, 3000),
                'size' => fake()->randomElement(['small', 'medium', 'large'])
            ]) : null,
        ];
    }
}
