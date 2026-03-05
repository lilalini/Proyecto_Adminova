<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Document;
use App\Models\User;
use App\Models\Owner;
use App\Models\Guest;
use App\Models\Accommodation;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {$documentableTypes = [User::class, Owner::class, Guest::class, Accommodation::class];
        $documentableType = fake()->randomElement($documentableTypes);
        $documentable = $documentableType::inRandomOrder()->first() ?? $documentableType::factory();
        
        $documentTypes = [
            User::class => ['contract', 'id_card', 'criminal_record'],
            Owner::class => ['contract', 'id_card', 'bank_statement', 'tax_document'],
            Guest::class => ['id_card', 'passport', 'visa'],
            Accommodation::class => ['license', 'insurance', 'energy_certificate', 'floor_plan'],
        ];
        
        $docType = fake()->randomElement($documentTypes[$documentableType]);
        $isVerified = fake()->boolean(70);
        
        return [
            'documentable_type' => $documentableType,
            'documentable_id' => $documentable instanceof $documentableType ? $documentable->id : $documentable->create()->id,
            'document_type' => $docType,
            'title' => ucfirst(str_replace('_', ' ', $docType)),
            'file_name' => fake()->slug() . '.pdf',
            'file_path' => 'documents/' . fake()->uuid() . '.pdf',
            'file_size' => fake()->numberBetween(100000, 5000000),
            'mime_type' => 'application/pdf',
            'is_signed' => in_array($docType, ['contract', 'license']) ? fake()->boolean(80) : false,
            'signed_at' => in_array($docType, ['contract', 'license']) && fake()->boolean(80) ? fake()->dateTimeThisYear() : null,
            'valid_from' => fake()->optional(0.6)->dateTimeBetween('-1 year', 'now'),
            'valid_until' => fake()->optional(0.4)->dateTimeBetween('now', '+5 years'),
            'is_verified' => $isVerified,
            'verified_by_user_id' => $isVerified ? User::inRandomOrder()->first()?->id : null,
            'verified_at' => $isVerified ? fake()->dateTimeThisYear() : null,
            'notes' => fake()->optional(0.3)->sentence(),
            'metadata' => fake()->optional(0.2)->json(),
        ];
    
    }
}
