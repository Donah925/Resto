<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class SearchService
{
    /**
     * Rechercher des restaurants
     */
    public function rechercherRestaurants(
        ?string $query = null,
        ?array $filtres = [],
        ?float $latitude = null,
        ?float $longitude = null,
        ?int $rayon = null,
        int $parPage = 20
    ) {
        $recherche = Restaurant::with(['categorie', 'adresse'])
            ->where('actif', true);

        // Recherche textuelle
        if ($query) {
            $recherche->where(function (Builder $q) use ($query) {
                $q->where('nom', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhereHas('categorie', function (Builder $cq) use ($query) {
                      $cq->where('nom', 'LIKE', "%{$query}%");
                  });
            });
        }

        // Filtres
        if (!empty($filtres)) {
            $this->appliquerFiltresRestaurants($recherche, $filtres);
        }

        // Recherche géolocalisée
        if ($latitude && $longitude && $rayon) {
            $this->filtrerParDistance($recherche, $latitude, $longitude, $rayon);
        }

        return $recherche->orderBy('note_moyenne', 'desc')
            ->paginate($parPage);
    }

    /**
     * Rechercher des produits
     */
    public function rechercherProduits(
        ?string $query = null,
        ?array $filtres = [],
        ?int $restaurantId = null,
        int $parPage = 20
    ) {
        $recherche = Produit::with(['categorie', 'restaurant'])
            ->where('actif', true);

        // Recherche textuelle
        if ($query) {
            $recherche->where(function (Builder $q) use ($query) {
                $q->where('nom', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
            });
        }

        // Filtre par restaurant
        if ($restaurantId) {
            $recherche->where('restaurant_id', $restaurantId);
        }

        // Filtres
        if (!empty($filtres)) {
            $this->appliquerFiltresProduits($recherche, $filtres);
        }

        return $recherche->orderBy('popularite', 'desc')
            ->paginate($parPage);
    }

    /**
     * Appliquer les filtres pour les restaurants
     */
    private function appliquerFiltresRestaurants(Builder $query, array $filtres): void
    {
        // Filtre par catégorie
        if (isset($filtres['categorie_id'])) {
            $query->where('categorie_id', $filtres['categorie_id']);
        }

        // Filtre par note minimale
        if (isset($filtres['note_min'])) {
            $query->where('note_moyenne', '>=', $filtres['note_min']);
        }

        // Filtre par prix
        if (isset($filtres['prix_min'])) {
            $query->where('prix_moyen', '>=', $filtres['prix_min']);
        }
        if (isset($filtres['prix_max'])) {
            $query->where('prix_moyen', '<=', $filtres['prix_max']);
        }

        // Filtre par type de service
        if (isset($filtres['livraison']) && $filtres['livraison']) {
            $query->where('livraison_disponible', true);
        }
        if (isset($filtres['emporter']) && $filtres['emporter']) {
            $query->where('emporter_disponible', true);
        }

        // Filtre par ouvert maintenant
        if (isset($filtres['ouvert']) && $filtres['ouvert']) {
            $now = now();
            $jourSemaine = strtolower($now->format('l'));
            $heure = $now->format('H:i');
            
            $query->whereJsonContains('horaires_ouverture->' . $jourSemaine . '.ferme', false)
                  ->whereJsonExtractPath('horaires_ouverture', $jourSemaine, 'ouverture', '<=', $heure)
                  ->whereJsonExtractPath('horaires_ouverture', $jourSemaine, 'fermeture', '>=', $heure);
        }

        // Filtres spéciaux
        if (isset($filtres['bio']) && $filtres['bio']) {
            $query->where('options->bio', true);
        }
        if (isset($filtres['vegetarien']) && $filtres['vegetarien']) {
            $query->where('options->options_vegetariennes', true);
        }
        if (isset($filtres['halal']) && $filtres['halal']) {
            $query->where('options->certification_halal', true);
        }
    }

    /**
     * Appliquer les filtres pour les produits
     */
    private function appliquerFiltresProduits(Builder $query, array $filtres): void
    {
        // Filtre par catégorie
        if (isset($filtres['categorie_id'])) {
            $query->where('categorie_id', $filtres['categorie_id']);
        }

        // Filtre par prix
        if (isset($filtres['prix_min'])) {
            $query->where('prix', '>=', $filtres['prix_min']);
        }
        if (isset($filtres['prix_max'])) {
            $query->where('prix', '<=', $filtres['prix_max']);
        }

        // Filtre par disponibilité
        if (isset($filtres['disponible']) && $filtres['disponible']) {
            $query->where('stock_disponible', true);
        }

        // Filtre par promotion
        if (isset($filtres['promotion']) && $filtres['promotion']) {
            $query->where('promotion_en_cours', true);
        }

        // Filtres alimentaires
        if (isset($filtres['vegetarien']) && $filtres['vegetarien']) {
            $query->where('options->vegetarien', true);
        }
        if (isset($filtres['vegan']) && $filtres['vegan']) {
            $query->where('options->vegan', true);
        }
        if (isset($filtres['sans_gluten']) && $filtres['sans_gluten']) {
            $query->where('options->sans_gluten', true);
        }
    }

    /**
     * Filtrer par distance (PostGIS ou calcul manuel)
     */
    private function filtrerParDistance(Builder $query, float $lat, float $lng, int $rayonKm): void
    {
        // Si PostGIS est disponible
        if (DB::connection()->getDriverName() === 'pgsql') {
            $query->whereRaw(
                "ST_Distance_Sphere(adresse->>'coordonnees', ST_MakePoint(?, ?)) <= ?",
                [$lng, $lat, $rayonKm * 1000]
            );
        } else {
            // Calcul Haversine pour MySQL/SQLite
            $query->selectRaw("*, 
                (6371 * acos(cos(radians(?)) * cos(radians(adresse->>'$.lat')) * 
                cos(radians(adresse->>'$.lng') - radians(?)) + 
                sin(radians(?)) * sin(radians(adresse->>'$.lat')))) AS distance",
                [$lat, $lng, $lat]
            )
            ->having('distance', '<=', $rayonKm)
            ->orderBy('distance');
        }
    }

    /**
     * Recherche full-text avancée
     */
    public function rechercheAvancee(string $query, array $types = ['restaurants', 'produits']): array
    {
        $resultats = [];

        if (in_array('restaurants', $types)) {
            $resultats['restaurants'] = $this->rechercherRestaurants($query, [], null, null, null, 10);
        }

        if (in_array('produits', $types)) {
            $resultats['produits'] = $this->rechercherProduits($query, [], null, 10);
        }

        return $resultats;
    }

    /**
     * Suggestions de recherche (autocomplete)
     */
    public function suggestionsRecherche(string $query, int $limit = 5): array
    {
        $suggestions = [];

        // Suggestions de restaurants
        $restaurants = Restaurant::where('nom', 'LIKE', "%{$query}%")
            ->where('actif', true)
            ->limit($limit)
            ->pluck('nom');

        // Suggestions de produits
        $produits = Produit::where('nom', 'LIKE', "%{$query}%")
            ->where('actif', true)
            ->limit($limit)
            ->pluck('nom');

        // Suggestions de catégories
        $categories = \App\Models\Categorie::where('nom', 'LIKE', "%{$query}%")
            ->limit($limit)
            ->pluck('nom');

        return [
            'restaurants' => $restaurants->toArray(),
            'produits' => $produits->toArray(),
            'categories' => $categories->toArray(),
        ];
    }

    /**
     * Rechercher par filtre rapide (populaires, mieux notés, etc.)
     */
    public function rechercheRapide(string $type, ?float $lat = null, ?float $lng = null): array
    {
        switch ($type) {
            case 'populaires':
                return Restaurant::where('actif', true)
                    ->orderBy('nombre_commandes', 'desc')
                    ->limit(20)
                    ->get();

            case 'mieux_notes':
                return Restaurant::where('actif', true)
                    ->orderBy('note_moyenne', 'desc')
                    ->limit(20)
                    ->get();

            case 'nouveautes':
                return Restaurant::where('actif', true)
                    ->orderBy('created_at', 'desc')
                    ->limit(20)
                    ->get();

            case 'promotions':
                return Produit::where('actif', true)
                    ->where('promotion_en_cours', true)
                    ->with('restaurant')
                    ->limit(20)
                    ->get();

            case 'autour_de_moi':
                if (!$lat || !$lng) {
                    return [];
                }
                return $this->rechercherRestaurants(null, [], $lat, $lng, 5)->items();

            default:
                return [];
        }
    }

    /**
     * Indexer un élément pour la recherche (pour Elasticsearch/Algolia)
     */
    public function indexerRestaurant(Restaurant $restaurant): void
    {
        // Implémentation pour moteur de recherche externe
        // Exemple avec Algolia:
        // $restaurant->searchable();
    }

    /**
     * Supprimer un élément de l'index
     */
    public function desindexerRestaurant(Restaurant $restaurant): void
    {
        // $restaurant->unsearchable();
    }
}
