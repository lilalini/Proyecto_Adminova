<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Document;
use App\Models\Owner;
use App\Models\Guest;
use App\Models\Accommodation;
use App\Models\User;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        
        // Documentos para owners
        $owners = Owner::all();
        foreach ($owners as $owner) {
            // Contrato firmado
            Document::create([
                'documentable_type' => Owner::class,
                'documentable_id' => $owner->id,
                'document_type' => 'contract',
                'title' => 'Contrato de colaboración',
                'file_name' => 'contract_' . $owner->id . '.pdf',
                'file_path' => 'documents/owners/contract_' . $owner->id . '.pdf',
                'file_size' => rand(200000, 500000),
                'mime_type' => 'application/pdf',
                'is_signed' => true,
                'signed_at' => $owner->contract_date,
                'valid_from' => $owner->contract_date,
                'valid_until' => $owner->contract_date ? date('Y-m-d', strtotime($owner->contract_date . ' +1 year')) : null,
                'is_verified' => true,
                'verified_by_user_id' => $admin?->id,
                'verified_at' => $owner->contract_date,
            ]);
            
            // DNI
            Document::create([
                'documentable_type' => Owner::class,
                'documentable_id' => $owner->id,
                'document_type' => 'id_card',
                'title' => 'DNI',
                'file_name' => 'id_' . $owner->id . '.pdf',
                'file_path' => 'documents/owners/id_' . $owner->id . '.pdf',
                'file_size' => rand(100000, 300000),
                'mime_type' => 'application/pdf',
                'is_verified' => true,
                'verified_by_user_id' => $admin?->id,
            ]);
        }
        
        // Documentos para guests (30%)
        $guests = Guest::all();
        foreach ($guests->random($guests->count() * 0.3) as $guest) {
            Document::create([
                'documentable_type' => Guest::class,
                'documentable_id' => $guest->id,
                'document_type' => fake()->randomElement(['id_card', 'passport']),
                'title' => 'Identificación',
                'file_name' => 'guest_id_' . $guest->id . '.pdf',
                'file_path' => 'documents/guests/id_' . $guest->id . '.pdf',
                'file_size' => rand(100000, 300000),
                'mime_type' => 'application/pdf',
                'is_verified' => fake()->boolean(80),
                'verified_by_user_id' => fake()->boolean(80) ? $admin?->id : null,
            ]);
        }
        
        // Documentos para accommodations
        $accommodations = Accommodation::all();
        foreach ($accommodations as $accommodation) {
            Document::create([
                'documentable_type' => Accommodation::class,
                'documentable_id' => $accommodation->id,
                'document_type' => 'license',
                'title' => 'Licencia turística',
                'file_name' => 'license_' . $accommodation->id . '.pdf',
                'file_path' => 'documents/accommodations/license_' . $accommodation->id . '.pdf',
                'file_size' => rand(100000, 300000),
                'mime_type' => 'application/pdf',
                'valid_from' => now()->subMonths(rand(1, 12)),
                'valid_until' => now()->addYears(rand(1, 5)),
                'is_verified' => true,
                'verified_by_user_id' => $admin?->id,
            ]);
        }
    
    }
}
