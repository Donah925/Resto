<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\Commande;
use App\Models\User;
use App\Models\Produit;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Obtenir les statistiques générales de la plateforme
     */
    public function getStatistiquesPlateforme(?Carbon $debut = null, ?Carbon $fin = null): array
    {
        $queryCommandes = Commande::query();
        
        if ($debut) {
            $queryCommandes->where('created_at', '>=', $debut);
        }
        if ($fin) {
            $queryCommandes->where('created_at', '<=', $fin);
        }

        $commandes = $queryCommandes->get();

        return [
            'total_commandes' => $commandes->count(),
            'chiffre_affaires' => $commandes->sum('total'),
            'panier_moyen' => $commandes->avg('total') ?? 0,
            'total_clients' => User::role('client')->count(),
            'total_restaurants' => Restaurant::count(),
            'total_livreurs' => User::role('livreur')->count(),
            'commandes_par_statut' => $commandes->groupBy('statut')->map->count(),
        ];
    }

    /**
     * Statistiques détaillées d'un restaurant
     */
    public function getStatistiquesRestaurant(Restaurant $restaurant, ?Carbon $debut = null, ?Carbon $fin = null): array
    {
        $queryCommandes = Commande::where('restaurant_id', $restaurant->id);

        if ($debut) {
            $queryCommandes->where('created_at', '>=', $debut);
        }
        if ($fin) {
            $queryCommandes->where('created_at', '<=', $fin);
        }

        $commandes = $queryCommandes->get();

        $produitsPopulaires = DB::table('commande_produit')
            ->join('produits', 'produits.id', '=', 'commande_produit.produit_id')
            ->where('produits.restaurant_id', $restaurant->id)
            ->select('produits.nom', DB::raw('SUM(commande_produit.quantite) as total_vendu'))
            ->groupBy('produits.id', 'produits.nom')
            ->orderByDesc('total_vendu')
            ->limit(10)
            ->get();

        return [
            'total_commandes' => $commandes->count(),
            'chiffre_affaires' => $commandes->sum('total'),
            'panier_moyen' => $commandes->avg('total') ?? 0,
            'note_moyenne' => $restaurant->avis()->avg('note') ?? 0,
            'total_avis' => $restaurant->avis()->count(),
            'produits_populaires' => $produitsPopulaires,
            'commandes_par_jour' => $this->getCommandesParJour($commandes),
            'heures_pointe' => $this->getHeuresPointe($commandes),
        ];
    }

    /**
     * Statistiques d'un livreur
     */
    public function getStatistiquesLivreur(User $livreur, ?Carbon $debut = null, ?Carbon $fin = null): array
    {
        $queryCommandes = Commande::where('livreur_id', $livreur->id);

        if ($debut) {
            $queryCommandes->where('created_at', '>=', $debut);
        }
        if ($fin) {
            $queryCommandes->where('created_at', '<=', $fin);
        }

        $commandes = $queryCommandes->get();

        return [
            'total_livraisons' => $commandes->where('statut', 'livree')->count(),
            'revenu_total' => $commandes->sum('frais_livraison'),
            'pourboires_total' => $commandes->sum('pourboire'),
            'duree_moyenne_livraison' => $commandes->avg(
                fn($c) => $c->date_livraison ? $c->date_livraison->diffInMinutes($c->created_at) : 0
            ),
            'taux_reussite' => $commandes->where('statut', 'livree')->count() / max(1, $commandes->count()) * 100,
        ];
    }

    /**
     * Statistiques d'un client
     */
    public function getStatistiquesClient(User $client): array
    {
        $commandes = $client->commandes;

        return [
            'total_commandes' => $commandes->count(),
            'depenses_totales' => $commandes->sum('total'),
            'panier_moyen' => $commandes->avg('total') ?? 0,
            'restaurants_frequentes' => $commandes->pluck('restaurant_id')->unique()->count(),
            'derniere_commande' => $commandes->sortByDesc('created_at')->first()?->created_at,
            'produits_favoris' => $this->getProduitsFrequents($client),
        ];
    }

    /**
     * Évolution du chiffre d'affaires
     */
    public function getEvolutionCA(?Carbon $debut = null, ?Carbon $fin = null, string $periode = 'jour'): array
    {
        $queryCommandes = Commande::where('statut', '!=', 'annulee');

        if ($debut) {
            $queryCommandes->where('created_at', '>=', $debut);
        }
        if ($fin) {
            $queryCommandes->where('created_at', '<=', $fin);
        }

        $format = match ($periode) {
            'jour' => 'Y-m-d',
            'semaine' => 'Y-W',
            'mois' => 'Y-m',
            default => 'Y-m-d'
        };

        return $queryCommandes->selectRaw("DATE_FORMAT(created_at, '%{$format}) as periode, SUM(total) as ca")
            ->groupBy('periode')
            ->orderBy('periode')
            ->get()
            ->toArray();
    }

    /**
     * Top restaurants par CA
     */
    public function getTopRestaurants(int $limit = 10, ?Carbon $debut = null, ?Carbon $fin = null): array
    {
        $query = Restaurant::withCount(['commandes' => function ($q) use ($debut, $fin) {
            if ($debut) {
                $q->where('created_at', '>=', $debut);
            }
            if ($fin) {
                $q->where('created_at', '<=', $fin);
            }
        }])
        ->withSum(['commandes' => function ($q) use ($debut, $fin) {
            if ($debut) {
                $q->where('created_at', '>=', $debut);
            }
            if ($fin) {
                $q->where('created_at', '<=', $fin);
            }
        }], 'total')
        ->orderByDesc('commandes_sum_total')
        ->limit($limit)
        ->get();

        return $query->toArray();
    }

    /**
     * Top produits vendus
     */
    public function getTopProduits(int $limit = 10, ?int $restaurantId = null, ?Carbon $debut = null, ?Carbon $fin = null): array
    {
        $query = DB::table('commande_produit')
            ->join('produits', 'produits.id', '=', 'commande_produit.produit_id')
            ->join('commandes', 'commandes.id', '=', 'commande_produit.commande_id');

        if ($restaurantId) {
            $query->where('produits.restaurant_id', $restaurantId);
        }
        if ($debut) {
            $query->where('commandes.created_at', '>=', $debut);
        }
        if ($fin) {
            $query->where('commandes.created_at', '<=', $fin);
        }

        return $query->select('produits.id', 'produits.nom', DB::raw('SUM(commande_produit.quantite) as total_vendu'))
            ->groupBy('produits.id', 'produits.nom')
            ->orderByDesc('total_vendu')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Analyse des annulations
     */
    public function getAnalyseAnnulations(?Carbon $debut = null, ?Carbon $fin = null): array
    {
        $queryCommandes = Commande::where('statut', 'annulee');

        if ($debut) {
            $queryCommandes->where('created_at', '>=', $debut);
        }
        if ($fin) {
            $queryCommandes->where('created_at', '<=', $fin);
        }

        $commandes = $queryCommandes->get();

        return [
            'total_annulations' => $commandes->count(),
            'taux_annulation' => $commandes->count() / max(1, Commande::count()) * 100,
            'annulations_par_motif' => $commandes->groupBy('motif_annulation')->map->count(),
            'montant_perdu' => $commandes->sum('total'),
        ];
    }

    /**
     * Commandes par jour de la semaine
     */
    private function getCommandesParJour($commandes): array
    {
        return $commandes->groupBy(fn($c) => $c->created_at->format('l'))
            ->map->count()
            ->toArray();
    }

    /**
     * Heures de pointe
     */
    private function getHeuresPointe($commandes): array
    {
        return $commandes->groupBy(fn($c) => $c->created_at->format('H:00'))
            ->map->count()
            ->sortDesc()
            ->take(5)
            ->toArray();
    }

    /**
     * Produits fréquents d'un client
     */
    private function getProduitsFrequents(User $client): array
    {
        return DB::table('commande_produit')
            ->join('commandes', 'commandes.id', '=', 'commande_produit.commande_id')
            ->join('produits', 'produits.id', '=', 'commande_produit.produit_id')
            ->where('commandes.client_id', $client->id)
            ->select('produits.nom', DB::raw('SUM(commande_produit.quantite) as total_commande'))
            ->groupBy('produits.id', 'produits.nom')
            ->orderByDesc('total_commande')
            ->limit(5)
            ->get()
            ->toArray();
    }

    /**
     * Taux de conversion (visiteurs -> commandes)
     */
    public function getTauxConversion(?Carbon $debut = null, ?Carbon $fin = null): float
    {
        // Nécessite un tracking des sessions/visiteurs
        $totalVisiteurs = 10000; // À remplacer par données réelles
        $totalClients = User::whereBetween('created_at', [$debut, $fin])->count();

        return $totalClients / max(1, $totalVisiteurs) * 100;
    }

    /**
     * Temps moyen de livraison
     */
    public function getTempsMoyenLivraison(?Carbon $debut = null, ?Carbon $fin = null, ?int $restaurantId = null): float
    {
        $query = Commande::whereNotNull('date_livraison')
            ->where('statut', 'livree');

        if ($debut) {
            $query->where('created_at', '>=', $debut);
        }
        if ($fin) {
            $query->where('created_at', '<=', $fin);
        }
        if ($restaurantId) {
            $query->where('restaurant_id', $restaurantId);
        }

        return $query->get()->avg(fn($c) => $c->date_livraison->diffInMinutes($c->created_at)) ?? 0;
    }

    /**
     * Générer un rapport complet
     */
    public function genererRapportComplet(string $type, array $options = []): array
    {
        switch ($type) {
            case 'journalier':
                return $this->genererRapportJournalier($options['date'] ?? now());
            case 'hebdomadaire':
                return $this->genererRapportHebdomadaire($options['semaine'] ?? now());
            case 'mensuel':
                return $this->genererRapportMensuel($options['mois'] ?? now());
            default:
                return [];
        }
    }

    /**
     * Rapport journalier
     */
    private function genererRapportJournalier(Carbon $date): array
    {
        $debut = $date->copy()->startOfDay();
        $fin = $date->copy()->endOfDay();

        return [
            'date' => $date->format('Y-m-d'),
            'statistiques' => $this->getStatistiquesPlateforme($debut, $fin),
            'evolution_hourly' => $this->getEvolutionCA($debut, $fin, 'heure'),
        ];
    }

    /**
     * Rapport hebdomadaire
     */
    private function genererRapportHebdomadaire(Carbon $date): array
    {
        $debut = $date->copy()->startOfWeek();
        $fin = $date->copy()->endOfWeek();

        return [
            'semaine' => $date->format('Y-W'),
            'statistiques' => $this->getStatistiquesPlateforme($debut, $fin),
            'evolution_journaliere' => $this->getEvolutionCA($debut, $fin, 'jour'),
        ];
    }

    /**
     * Rapport mensuel
     */
    private function genererRapportMensuel(Carbon $date): array
    {
        $debut = $date->copy()->startOfMonth();
        $fin = $date->copy()->endOfMonth();

        return [
            'mois' => $date->format('Y-m'),
            'statistiques' => $this->getStatistiquesPlateforme($debut, $fin),
            'top_restaurants' => $this->getTopRestaurants(10, $debut, $fin),
            'top_produits' => $this->getTopProduits(10, null, $debut, $fin),
        ];
    }
}
