<?php

namespace App\Console\Commands;

use App\Models\Accommodation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GeocodeAddresses extends Command
{
    protected $signature = 'geocode:addresses';
    protected $description = 'Geocode all accommodations without coordinates';

    public function handle()
    {
        $accommodations = Accommodation::whereRaw("
            latitude IS NULL 
            OR latitude::text = '' 
            OR latitude = 0 
            OR longitude IS NULL 
            OR longitude::text = '' 
            OR longitude = 0
        ")->get();

        $this->info("Encontrados {$accommodations->count()} alojamientos sin coordenadas");

        foreach ($accommodations as $acc) {
            $address = "{$acc->address}, {$acc->city}, {$acc->country}";
            
            $this->info("Geocoding: {$address}");
            
            $response = Http::withHeaders([
                'User-Agent' => 'Adminova/1.0'
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $address,
                'format' => 'json',
                'limit' => 1
            ]);

            if ($response->successful() && count($response->json()) > 0) {
                $data = $response->json()[0];
                
                $acc->latitude = $data['lat'];
                $acc->longitude = $data['lon'];
                $acc->save();
                
                $this->info("{$acc->title}: {$data['lat']}, {$data['lon']}");
            } else {
                $this->error("No se encontró: {$address}");
            }
            
            sleep(1);
        }
        
        $this->info('Geocodificación completada');
    }
}