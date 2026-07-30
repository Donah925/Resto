<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Livraison;
use Illuminate\Http\Request;

class LivraisonController extends Controller
{
    public function index(Request $request)
    {
        $livraisons = $request->user()->profilClient->commandes()
            ->whereHas('livraison')
            ->with(['livraison.livreur.utilisateur', 'livraison.suivis'])
            ->latest()
            ->paginate($request->input('per_page', 20));

        return response()->json(['livraisons' => $livraisons]);
    }

    public function show(Livraison $livraison, Request $request)
    {
        if ($livraison->commande->client_id !== $request->user()->profilClient->id) {
            abort(403);
        }

        $livraison->load(['suivis' => fn($q) => $q->latest(), 'livreur.utilisateur']);

        return response()->json(['livraison' => $livraison]);
    }
}
