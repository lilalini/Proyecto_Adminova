<?php

namespace App\Console\Commands;

use App\Models\Accommodation;
use App\Services\GeocodingService;
use Illuminate\Console\Command;

class GeocodeAddresses extends Command
{
    protected $signature = 'geocode:addresses';
    protected $description = 'Geocode all accommodations without coordinates';

    public function __construct(protected GeocodingService $geocodingService)
    {
        parent::__construct();
    }

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

            $coordinates = $this->geocodingService->geocodeAddress($address);

            if ($coordinates) {
                $acc->latitude = $coordinates['lat'];
                $acc->longitude = $coordinates['lon'];
                $acc->save();
                $this->info("{$acc->title}: {$coordinates['lat']}, {$coordinates['lon']}");
            } else {
                $this->error("No se encontró: {$address}");
            }

            sleep(1);
        }

        $this->info('Geocodificación completada');
    }
}