<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Panier;
use App\Models\Produit;
use App\Models\CodePromo;
use Illuminate\Http\Request;

class PanierController extends Controller
{
    public function show(Request $request)
    {
        $client = $request->user()->profilClient;
        $panier = $this->recupererOuCreerPanier($client, $request);

        $panier->load([
            'restaurant',
            'lignes.produit.images',
            'lignes.variante',
            'codePromo',
        ]);

        return response()->json(['panier' => $panier]);
    }

    public function ajouter(Request $request)
    {
        $donnees = $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'restaurant_id' => 'required|exists:restaurants,id',
            'variante_id' => 'nullable|exists:variantes_produit,id',
            'quantite' => 'required|integer|min:1|max:20',
            'options' => 'nullable|array',
            'notes' => 'nullable|string|max:255',
        ]);

        $client = $request->user()->profilClient;
        $produit = Produit::with(['variantes', 'supplements'])->findOrFail($donnees['produit_id']);

        if (!$produit->est_disponible || !$produit->estEnStock()) {
            return response()->json(['message' => 'Ce produit n\'est pas disponible'], 422);
        }

        $panier = $this->recupererOuCreerPanier($client, $request, $donnees['restaurant_id']);

        $ligneExistante = $panier->lignes()
            ->where('produit_id', $donnees['produit_id'])
            ->where('variante_id', $donnees['variante_id'] ?? null)
            ->first();

        if ($ligneExistante) {
            $ligneExistante->increment('quantite', $donnees['quantite']);
        } else {
            $prix = $produit->getPrixPourRestaurant($donnees['restaurant_id']) ?? $produit->prix_base;
            
            $panier->lignes()->create([
                'produit_id' => $donnees['produit_id'],
                'variante_id' => $donnees['variante_id'] ?? null,
                'quantite' => $donnees['quantite'],
                'prix_unitaire' => $prix,
                'options' => $donnees['options'] ?? null,
                'notes' => $donnees['notes'] ?? null,
            ]);
        }

        $panier->recalculerTotal();

        return response()->json(['message' => 'Produit ajouté au panier', 'panier' => $panier]);
    }

    public function modifier(Request $request, Panier $ligne)
    {
        $client = $request->user()->profilClient;
        $panier = $this->recupererOuCreerPanier($client, $request);

        if ($ligne->panier_id !== $panier->id) {
            abort(403);
        }

        $request->validate([
            'quantite' => 'required|integer|min:1|max:20',
        ]);

        $ligne->update(['quantite' => $request->quantite]);
        $panier->recalculerTotal();

        return response()->json(['message' => 'Panier mis à jour', 'panier' => $panier]);
    }

    public function supprimer(Request $request, Panier $ligne)
    {
        $client = $request->user()->profilClient;
        $panier = $this->recupererOuCreerPanier($client, $request);

        if ($ligne->panier_id !== $panier->id) {
            abort(403);
        }

        $ligne->delete();
        $panier->recalculerTotal();

        return response()->json(['message' => 'Produit retiré du panier']);
    }

    public function vider(Request $request)
    {
        $client = $request->user()->profilClient;
        $panier = $this->recupererOuCreerPanier($client, $request);

        $panier->lignes()->delete();
        $panier->update(['code_promo_id' => null]);
        $panier->recalculerTotal();

        return response()->json(['message' => 'Panier vidé']);
    }

    public function appliquerCodePromo(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $client = $request->user()->profilClient;
        $panier = $this->recupererOuCreerPanier($client, $request);

        $codePromo = CodePromo::where('code', strtoupper($request->code))->first();

        if (!$codePromo) {
            return response()->json(['message' => 'Code promo invalide'], 422);
        }

        $commandeTemporaire = new \App\Models\Commande([
            'client_id' => $client->id,
            'restaurant_id' => $panier->restaurant_id,
            'sous_total' => $panier->sous_total,
        ]);

        if (!$codePromo->estValidePour($commandeTemporaire)) {
            return response()->json(['message' => 'Code promo non applicable'], 422);
        }

        $panier->update(['code_promo_id' => $codePromo->id]);
        $panier->recalculerTotal();

        return response()->json(['message' => 'Code promo appliqué', 'panier' => $panier]);
    }

    private function recupererOuCreerPanier($client, $request, ?string $restaurantId = null): Panier
    {
        $query = Panier::where('client_id', $client->id);

        if ($restaurantId) {
            $query->where('restaurant_id', $restaurantId);
        }

        return $query->firstOrCreate(
            $restaurantId ? ['restaurant_id' => $restaurantId] : [],
            [
                'restaurant_id' => $restaurantId ?? $request->input('restaurant_id'),
                'session_id' => $request->session()?->getId(),
            ]
        );
    }
}
