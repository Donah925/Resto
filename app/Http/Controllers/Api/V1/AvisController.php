<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Avis;
use App\Models\Commande;
use Illuminate\Http\Request;

class AvisController extends Controller
{
    public function store(Request $request)
    {
        $donnees = $request->validate([
            'commande_id' => 'required|exists:commandes,id',
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:1000',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|max:2048',
        ]);

        $client = $request->user()->profilClient;
        $commande = Commande::findOrFail($donnees['commande_id']);

        if ($commande->client_id !== $client->id) {
            abort(403, 'Vous ne pouvez pas donner un avis sur cette commande');
        }

        // Vérifier si la commande est terminée
        if (!$commande->statut->estTerminee()) {
            return response()->json([
                'message' => 'Vous ne pouvez donner un avis que sur une commande terminée',
            ], 422);
        }

        // Vérifier si un avis existe déjà
        if (Avis::where('commande_id', $commande->id)->exists()) {
            return response()->json([
                'message' => 'Vous avez déjà donné un avis pour cette commande',
            ], 422);
        }

        $avis = Avis::create([
            'client_id' => $client->id,
            'restaurant_id' => $commande->restaurant_id,
            'commande_id' => $commande->id,
            'note' => $donnees['note'],
            'commentaire' => $donnees['commentaire'] ?? null,
            'statut' => 'visible',
        ]);

        // Upload des photos
        if (!empty($donnees['photos'])) {
            foreach ($donnees['photos'] as $photo) {
                $chemin = $photo->store('avis-photos', 'public');
                $avis->photos()->create(['chemin_image' => $chemin]);
            }
        }

        // Mettre à jour la note du restaurant
        $commande->restaurant->recalculerNote();

        return response()->json([
            'message' => 'Avis ajouté avec succès',
            'avis' => $avis->load(['photos']),
        ], 201);
    }
}
