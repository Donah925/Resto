<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Enums\StatutCommande;
use App\Enums\TypeCommande;
use App\Models\Commande;
use App\Models\Panier;
use App\Services\OrderService;
use Illuminate\Http\Request;

class CommandeController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function index(Request $request)
    {
        $commandes = $request->user()->profilClient
            ->commandes()
            ->with(['restaurant', 'lignes.produit', 'livraison'])
            ->latest()
            ->paginate($request->input('per_page', 20));

        return response()->json(['commandes' => $commandes]);
    }

    public function show(Commande $commande, Request $request)
    {
        $this->autoriserAcces($commande, $request->user());

        $commande->load([
            'restaurant',
            'lignes.produit.images',
            'lignes.variante',
            'lignes.options',
            'adresseLivraison',
            'livraison.suivis',
            'livraison.livreur.utilisateur',
            'paiement',
            'codePromo',
        ]);

        return response()->json(['commande' => $commande]);
    }

    public function store(Request $request)
    {
        $donnees = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'type_commande' => 'required|in:livraison,retrait,sur_place',
            'adresse_livraison_id' => 'required_if:type_commande,livraison|exists:adresses,id',
            'table_id' => 'required_if:type_commande,sur_place|exists:tables_restaurant,id',
            'code_promo' => 'nullable|string',
            'notes_client' => 'nullable|string|max:500',
            'methode_paiement' => 'required|string',
            'telephone_mobile_money' => 'nullable|string',
            'utiliser_points_fidelite' => 'nullable|boolean',
            'utiliser_portefeuille' => 'nullable|boolean',
        ]);

        $client = $request->user()->profilClient;
        $panier = Panier::where('client_id', $client->id)
            ->where('restaurant_id', $donnees['restaurant_id'])
            ->with('lignes')
            ->first();

        if (!$panier || $panier->lignes->isEmpty()) {
            return response()->json(['message' => 'Votre panier est vide'], 422);
        }

        try {
            $commande = $this->orderService->creerDepuisPanier($client, $panier, $donnees);

            return response()->json([
                'message' => 'Commande créée avec succès',
                'commande' => $commande->load(['paiement', 'livraison']),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la création de la commande',
                'erreur' => $e->getMessage(),
            ], 422);
        }
    }

    public function annuler(Commande $commande, Request $request)
    {
        $this->autoriserAcces($commande, $request->user());

        if (!$commande->estAnnulable()) {
            return response()->json([
                'message' => 'Cette commande ne peut plus être annulée',
            ], 422);
        }

        $this->orderService->annuler($commande, 'Annulation par le client');

        return response()->json(['message' => 'Commande annulée']);
    }

    public function suivi(Commande $commande, Request $request)
    {
        $this->autoriserAcces($commande, $request->user());

        $commande->load([
            'livraison.suivis' => fn($q) => $q->latest()->take(50),
            'livraison.livreur.utilisateur',
        ]);

        return response()->json([
            'statut' => $commande->statut,
            'statut_label' => $commande->statut->label(),
            'livraison' => $commande->livraison,
            'estimation' => $commande->temps_preparation_estime,
        ]);
    }

    public function recommander(Commande $commande, Request $request)
    {
        $this->autoriserAcces($commande, $request->user());

        $client = $request->user()->profilClient;

        // Créer un nouveau panier avec les mêmes produits
        $panier = Panier::create([
            'client_id' => $client->id,
            'restaurant_id' => $commande->restaurant_id,
            'session_id' => $request->session()?->getId(),
        ]);

        foreach ($commande->lignes as $ligne) {
            $panier->lignes()->create([
                'produit_id' => $ligne->produit_id,
                'variante_id' => $ligne->variante_id,
                'quantite' => $ligne->quantite,
                'prix_unitaire' => $ligne->produit->prix_actuel,
                'options' => $ligne->options->map(fn($o) => [
                    'id' => $o->supplement_id,
                    'nom' => $o->nom_supplement,
                    'prix' => $o->prix,
                ])->toArray(),
                'notes' => $ligne->notes,
            ]);
        }

        $panier->recalculerTotal();

        return response()->json([
            'message' => 'Produits ajoutés au panier',
            'panier_id' => $panier->id,
        ]);
    }

    private function autoriserAcces(Commande $commande, $user): void
    {
        if ($commande->client_id !== $user->profilClient->id) {
            abort(403, 'Vous n\'avez pas accès à cette commande');
        }
    }
}
