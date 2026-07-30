<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use Illuminate\Http\Request;

class FavorisController extends Controller
{
    public function index(Request $request)
    {
        $produits = $request->user()->profilClient->favoris()
            ->with(['images', 'categorie'])
            ->latest('pivot_cree_le')
            ->paginate($request->input('per_page', 20));

        return response()->json(['favoris' => $produits]);
    }

    public function toggle(Produit $produit, Request $request)
    {
        $client = $request->user()->profilClient;

        if ($client->favoris()->where('produit_id', $produit->id)->exists()) {
            $client->favoris()->detach($produit);
            return response()->json(['message' => 'Produit retiré des favoris']);
        }

        $client->favoris()->attach($produit->id);
        return response()->json(['message' => 'Produit ajouté aux favoris']);
    }
}
