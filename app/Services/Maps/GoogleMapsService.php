<?php

namespace App\Services\Maps;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GoogleMapsService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.google.maps_api_key');
        $this->baseUrl = 'https://maps.googleapis.com/maps/api';
    }

    /**
     * Géocoder une adresse
     *
     * @param string $address L'adresse à géocoder
     * @return array Coordonnées et informations
     */
    public function geocode(string $address): array
    {
        try {
            $response = Http::get($this->baseUrl . '/geocode/json', [
                'address' => $address,
                'key' => $this->apiKey,
            ]);

            if ($response->successful() && $response->json('status') === 'OK') {
                $result = $response->json('results.0');
                
                return [
                    'success' => true,
                    'latitude' => $result['geometry']['location']['lat'],
                    'longitude' => $result['geometry']['location']['lng'],
                    'formatted_address' => $result['formatted_address'],
                ];
            }

            return [
                'success' => false,
                'error' => 'Adresse non trouvée',
            ];
        } catch (Exception $e) {
            Log::error('Erreur géocodage Google Maps', [
                'address' => $address,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec Google Maps',
            ];
        }
    }

    /**
     * Calculer la distance entre deux points
     *
     * @param float $originLat Latitude du point de départ
     * @param float $originLng Longitude du point de départ
     * @param float $destLat Latitude du point d'arrivée
     * @param float $destLng Longitude du point d'arrivée
     * @return array Distance et durée estimée
     */
    public function calculateDistance(
        float $originLat,
        float $originLng,
        float $destLat,
        float $destLng
    ): array {
        try {
            $response = Http::get($this->baseUrl . '/distancematrix/json', [
                'origins' => "{$originLat},{$originLng}",
                'destinations' => "{$destLat},{$destLng}",
                'mode' => 'driving',
                'key' => $this->apiKey,
            ]);

            if ($response->successful() && $response->json('status') === 'OK') {
                $element = $response->json('rows.0.elements.0');
                
                return [
                    'success' => true,
                    'distance' => $element['distance']['value'], // en mètres
                    'distance_text' => $element['distance']['text'],
                    'duration' => $element['duration']['value'], // en secondes
                    'duration_text' => $element['duration']['text'],
                ];
            }

            return [
                'success' => false,
                'error' => 'Impossible de calculer la distance',
            ];
        } catch (Exception $e) {
            Log::error('Erreur calcul distance Google Maps', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec Google Maps',
            ];
        }
    }

    /**
     * Obtenir les détails d'un lieu
     *
     * @param string $placeId L'ID du lieu
     * @return array Détails du lieu
     */
    public function getPlaceDetails(string $placeId): array
    {
        try {
            $response = Http::get($this->baseUrl . '/place/details/json', [
                'place_id' => $placeId,
                'key' => $this->apiKey,
            ]);

            if ($response->successful() && $response->json('status') === 'OK') {
                return [
                    'success' => true,
                    'data' => $response->json('result'),
                ];
            }

            return [
                'success' => false,
                'error' => 'Lieu non trouvé',
            ];
        } catch (Exception $e) {
            Log::error('Erreur détails lieu Google Maps', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec Google Maps',
            ];
        }
    }

    /**
     * Rechercher des lieux à proximité
     *
     * @param float $lat Latitude
     * @param float $lng Longitude
     * @param string $type Type de lieu (restaurant, cafe, etc.)
     * @param int $radius Rayon de recherche en mètres
     * @return array Liste des lieux trouvés
     */
    public function searchNearby(
        float $lat,
        float $lng,
        string $type = 'restaurant',
        int $radius = 5000
    ): array {
        try {
            $response = Http::get($this->baseUrl . '/place/nearbysearch/json', [
                'location' => "{$lat},{$lng}",
                'radius' => $radius,
                'type' => $type,
                'key' => $this->apiKey,
            ]);

            if ($response->successful() && $response->json('status') === 'OK') {
                return [
                    'success' => true,
                    'places' => $response->json('results'),
                ];
            }

            return [
                'success' => false,
                'error' => 'Aucun lieu trouvé',
            ];
        } catch (Exception $e) {
            Log::error('Erreur recherche lieux Google Maps', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec Google Maps',
            ];
        }
    }

    /**
     * Générer une URL de carte statique
     *
     * @param float $lat Latitude du centre
     * @param float $lng Longitude du centre
     * @param int $zoom Niveau de zoom
     * @param int $width Largeur de l'image
     * @param int $height Hauteur de l'image
     * @return string URL de la carte
     */
    public function getStaticMapUrl(
        float $lat,
        float $lng,
        int $zoom = 15,
        int $width = 600,
        int $height = 400
    ): string {
        return sprintf(
            '%s/staticmap?center=%s,%s&zoom=%d&size=%dx%d&markers=color:red%%7C%s,%s&key=%s',
            $this->baseUrl,
            $lat,
            $lng,
            $zoom,
            $width,
            $height,
            $lat,
            $lng,
            $this->apiKey
        );
    }
}
