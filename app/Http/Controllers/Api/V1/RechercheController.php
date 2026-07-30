<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Produit;
use Illuminate\Http\Request;

class RechercheController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
            'type' => 'nullable|in:restaurants,produits,les_deux',
        ]);

        $query = $request->input('q');
        $type = $request->input('type', 'les_deux');

        $resultats = [];

        if (in_array($type, ['restaurants', 'les_deux'])) {
            $resultats['restaurants'] = Restaurant::rechercher($query)
                ->actifs()
                ->with(['images'])
                ->limit(10)
                ->get();
        }

        if (in_array($type, ['produits', 'les_deux'])) {
            $resultats['produits'] = Produit::rechercher($query)
                ->disponibles()
                ->with(['images', 'categorie'])
                ->limit(20)
                ->get();
        }

        return response()->json(['resultats' => $resultats]);
    }

    public function produits(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
            'restaurant_id' => 'nullable|exists:restaurants,id',
            'categorie_id' => 'nullable|exists:categories,id',
        ]);

        $produits = Produit::rechercher($request->input('q'))
            ->disponibles()
            ->when($request->filled('restaurant_id'), fn($q) => $q->whereHas('restaurants', fn($qr) => $qr->where('restaurants.id', $request->restaurant_id)))
            ->when($request->filled('categorie_id'), fn($q) => $q->where('categorie_id', $request->categorie_id))
            ->with(['images', 'categorie'])
            ->paginate($request->input('per_page', 20));

        return response()->json(['produits' => $produits]);
    }
}
