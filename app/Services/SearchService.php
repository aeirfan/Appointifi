<?php

namespace App\Services;

use App\Models\Business;
use Illuminate\Support\Collection;

class SearchService
{
    /**
     * Search for businesses based on query and location.
     * 
     * @param string|null $query - Search text (business name or service name)
     * @param float|null $latitude - User's latitude for geo-proximity search
     * @param float|null $longitude - User's longitude for geo-proximity search
     * @param string $sortBy - 'distance' or 'name'
     * @return Collection
     */
    public function searchBusinesses(?string $query, ?float $latitude, ?float $longitude, string $sortBy = 'name'): Collection
    {
        $businesses = Business::with(['locations', 'services'])
            ->when($query, function ($q) use ($query) {
                // Search by business name OR service name
                $q->where('name', 'like', "%{$query}%")
                  ->orWhereHas('services', function ($serviceQuery) use ($query) {
                      $serviceQuery->where('name', 'like', "%{$query}%");
                  });
            })
            ->get();

        // Calculate distance if user provided location
        if ($latitude && $longitude) {
            $businesses = $businesses->map(function ($business) use ($latitude, $longitude) {
                $location = $business->locations->first();
                
                if ($location && $location->latitude && $location->longitude) {
                    $business->distance = $this->calculateDistance(
                        $latitude,
                        $longitude,
                        $location->latitude,
                        $location->longitude
                    );
                } else {
                    $business->distance = null; // No location data
                }
                
                return $business;
            });
        }

        // Sort results
        if ($sortBy === 'distance' && $latitude && $longitude) {
            // Sort by distance (nulls last)
            $businesses = $businesses->sortBy(function ($business) {
                return $business->distance ?? PHP_FLOAT_MAX;
            });
        } else {
            // Sort by name
            $businesses = $businesses->sortBy('name');
        }

        return $businesses->values(); // Re-index collection
    }

    /**
     * Calculate distance between two coordinates using Haversine formula.
     * Returns distance in kilometers.
     * 
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float Distance in kilometers
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $latDiff = deg2rad($lat2 - $lat1);
        $lonDiff = deg2rad($lon2 - $lon1);

        $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDiff / 2) * sin($lonDiff / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    /**
     * Geocode an address to lat/lng using OpenStreetMap Nominatim.
     * Used to convert user's city/address input to coordinates.
     */
    public function geocodeAddress(string $address): ?array
    {
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'User-Agent' => 'Appointifi App (Laravel)',
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $address,
                'format' => 'json',
                'limit' => 1,
            ]);

            if ($response->successful() && count($response->json()) > 0) {
                $result = $response->json()[0];
                return [
                    'latitude' => (float) $result['lat'],
                    'longitude' => (float) $result['lon'],
                ];
            }
        } catch (\Exception $e) {
            // Geocoding failed, return null
        }

        return null;
    }
}
