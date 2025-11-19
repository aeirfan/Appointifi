<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Service for geocoding addresses using OpenStreetMap Nominatim API.
 * Converts address strings to latitude/longitude coordinates.
 */
class GeocodingService
{
    /**
     * Geocode an address to get latitude and longitude coordinates.
     * 
     * @param string $address The street address
     * @param string $city The city name
     * @return array|null Returns ['latitude' => float, 'longitude' => float] or null on failure
     */
    public function geocodeAddress(string $address, string $city): ?array
    {
        try {
            $query = trim($address . ', ' . $city);
            $response = Http::withHeaders([
                'User-Agent' => 'appointifi-app/1.0 (contact@localhost)'
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $query,
                'format' => 'json',
                'limit' => 1,
            ]);

            if ($response->ok()) {
                $result = $response->json();
                if (is_array($result) && !empty($result)) {
                    $lat = isset($result[0]['lat']) ? (float) $result[0]['lat'] : null;
                    $lng = isset($result[0]['lon']) ? (float) $result[0]['lon'] : null;
                    
                    if ($lat !== null && $lng !== null) {
                        return [
                            'latitude' => $lat,
                            'longitude' => $lng,
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            // Geocoding is best-effort; return null on any errors
            return null;
        }

        return null;
    }
}
