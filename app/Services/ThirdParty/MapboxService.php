<?php

namespace App\Services\ThirdParty;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MapboxService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected bool $enabled;

    public function __construct()
    {
        $this->enabled = config('services.mapbox.enabled', false);
        $this->apiKey = config('services.mapbox.api_key');
        $this->baseUrl = 'https://api.mapbox.com';
    }

    /**
     * Géocoder une adresse (obtenir les coordonnées)
     */
    public function geocodeAddress(string $address): array
    {
        if (!$this->enabled) {
            // Coordonnées par défaut (Paris) si désactivé
            return [
                'success' => true,
                'latitude' => 48.8566,
                'longitude' => 2.3522,
                'formatted_address' => $address,
                'message' => 'Géocodage simulé (Mapbox désactivé)',
            ];
        }

        try {
            $response = Http::get("{$this->baseUrl}/geocoding/v5/mapbox.places/{$address}.json", [
                'access_token' => $this->apiKey,
                'limit' => 1,
                'language' => 'fr',
            ]);

            $data = $response->json();

            if (empty($data['features'])) {
                return [
                    'success' => false,
                    'error' => 'Adresse non trouvée',
                ];
            }

            $feature = $data['features'][0];
            [$longitude, $latitude] = $feature['center'];

            Log::info('Adresse géocodée avec succès', [
                'address' => $address,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);

            return [
                'success' => true,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'formatted_address' => $feature['place_name'] ?? $address,
                'context' => $feature['context'] ?? [],
            ];
        } catch (Exception $e) {
            Log::error('Erreur géocodage Mapbox', [
                'address' => $address,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Impossible de géocoder l\'adresse',
            ];
        }
    }

    /**
     * Calculer la distance et le temps entre deux points
     */
    public function calculateDistance(
        float $fromLat,
        float $fromLng,
        float $toLat,
        float $toLng
    ): array {
        if (!$this->enabled) {
            // Estimation simple si désactivé
            $distance = $this->haversineDistance($fromLat, $fromLng, $toLat, $toLng);
            
            return [
                'success' => true,
                'distance_km' => round($distance, 2),
                'duration_minutes' => round($distance * 3, 0), // Estimation: 3 min/km
                'message' => 'Calcul simulé (Mapbox désactivé)',
            ];
        }

        try {
            $response = Http::get("{$this->baseUrl}/directions/v5/mapbox/driving/{$fromLng},{$fromLat};{$toLng},{$toLat}", [
                'access_token' => $this->apiKey,
                'overview' => 'false',
                'steps' => 'false',
            ]);

            $data = $response->json();

            if (empty($data['routes'])) {
                return [
                    'success' => false,
                    'error' => 'Itinéraire non trouvé',
                ];
            }

            $route = $data['routes'][0];
            $distanceKm = $route['distance'] / 1000;
            $durationMinutes = $route['duration'] / 60;

            Log::info('Distance calculée avec succès', [
                'distance_km' => round($distanceKm, 2),
                'duration_minutes' => round($durationMinutes, 0),
            ]);

            return [
                'success' => true,
                'distance_km' => round($distanceKm, 2),
                'duration_minutes' => round($durationMinutes, 0),
                'distance_meters' => $route['distance'],
                'duration_seconds' => $route['duration'],
            ];
        } catch (Exception $e) {
            Log::error('Erreur calcul distance Mapbox', [
                'error' => $e->getMessage(),
            ]);

            // Fallback avec Haversine
            $distance = $this->haversineDistance($fromLat, $fromLng, $toLat, $toLng);
            
            return [
                'success' => true,
                'distance_km' => round($distance, 2),
                'duration_minutes' => round($distance * 3, 0),
                'message' => 'Calcul avec fallback',
            ];
        }
    }

    /**
     * Obtenir un itinéraire détaillé
     */
    public function getRoute(
        float $fromLat,
        float $fromLng,
        float $toLat,
        float $toLng
    ): array {
        if (!$this->enabled) {
            return [
                'success' => true,
                'coordinates' => [[$fromLng, $fromLat], [$toLng, $toLat]],
                'message' => 'Itinéraire simulé (Mapbox désactivé)',
            ];
        }

        try {
            $response = Http::get("{$this->baseUrl}/directions/v5/mapbox/driving/{$fromLng},{$fromLat};{$toLng},{$toLat}", [
                'access_token' => $this->apiKey,
                'overview' => 'full',
                'steps' => 'true',
                'geometries' => 'geojson',
            ]);

            $data = $response->json();

            if (empty($data['routes'])) {
                return [
                    'success' => false,
                    'error' => 'Itinéraire non trouvé',
                ];
            }

            $route = $data['routes'][0];

            return [
                'success' => true,
                'geometry' => $route['geometry'],
                'distance_km' => round($route['distance'] / 1000, 2),
                'duration_minutes' => round($route['duration'] / 60, 0),
                'steps' => $route['legs'][0]['steps'] ?? [],
            ];
        } catch (Exception $e) {
            Log::error('Erreur récupération itinéraire Mapbox', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Impossible de récupérer l\'itinéraire',
            ];
        }
    }

    /**
     * Recherche d'adresses (autocomplete)
     */
    public function searchAddresses(string $query, ?float $latitude = null, ?float $longitude = null): array
    {
        if (!$this->enabled) {
            return [
                'success' => true,
                'results' => [],
                'message' => 'Recherche simulée (Mapbox désactivé)',
            ];
        }

        try {
            $params = [
                'access_token' => $this->apiKey,
                'limit' => 5,
                'language' => 'fr',
            ];

            if ($latitude && $longitude) {
                $params['proximity'] = "{$longitude},{$latitude}";
            }

            $response = Http::get("{$this->baseUrl}/geocoding/v5/mapbox.places/{$query}.json", $params);
            $data = $response->json();

            $results = collect($data['features'] ?? [])->map(function ($feature) {
                [$longitude, $latitude] = $feature['center'];
                
                return [
                    'formatted_address' => $feature['place_name'],
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'name' => $feature['text'],
                    'type' => $feature['place_type'][0] ?? 'address',
                ];
            })->toArray();

            return [
                'success' => true,
                'results' => $results,
            ];
        } catch (Exception $e) {
            Log::error('Erreur recherche adresses Mapbox', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Impossible de rechercher les adresses',
            ];
        }
    }

    /**
     * Calcul de distance à vol d'oiseau (formule Haversine)
     */
    protected function haversineDistance(
        float $lat1,
        float $lng1,
        float $lat2,
        float $lng2
    ): float {
        $earthRadius = 6371; // Rayon de la terre en km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Obtenir une carte statique (image)
     */
    public function getStaticMap(
        float $latitude,
        float $longitude,
        int $zoom = 15,
        int $width = 600,
        int $height = 400
    ): string {
        if (!$this->enabled) {
            return '';
        }

        return "{$this->baseUrl}/styles/v1/mapbox/streets-v11/static/{$longitude},{$latitude},{$zoom}/{$width}x{$height}?access_token={$this->apiKey}";
    }
}
