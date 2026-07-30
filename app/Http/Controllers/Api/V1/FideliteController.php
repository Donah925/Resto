<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FideliteController extends Controller
{
    public function show(Request $request)
    {
        $fidelite = $request->user()->profilClient->fidelite;

        return response()->json([
            'fidelite' => $fidelite,
            'points_disponibles' => $fidelite?->points_disponibles ?? 0,
            'niveau' => $fidelite?->niveau ?? 'bronze',
        ]);
    }

    public function historique(Request $request)
    {
        $historique = $request->user()->profilClient->fidelite
            ->historique()
            ->latest()
            ->paginate($request->input('per_page', 20));

        return response()->json(['historique' => $historique]);
    }

    public function convertirEnReduction(Request $request)
    {
        $request->validate([
            'points_a_utiliser' => 'required|integer|min:100',
        ]);

        $client = $request->user()->profilClient;
        $fidelite = $client->fidelite;

        if (!$fidelite || $fidelite->points_disponibles < $request->points_a_utiliser) {
            return response()->json([
                'message' => 'Points insuffisants',
            ], 422);
        }

        // Conversion : 100 points = 500 FCFA
        $reduction = ($request->points_a_utiliser / 100) * 500;

        $fidelite->decrement('points_disponibles', $request->points_a_utiliser);
        $fidelite->historique()->create([
            'type' => 'conversion',
            'points' => -$request->points_a_utiliser,
            'solde_apres' => $fidelite->points_disponibles,
            'description' => "Conversion en réduction de {$reduction} FCFA",
        ]);

        return response()->json([
            'message' => "Points convertis en réduction de {$reduction} FCFA",
            'reduction' => $reduction,
            'points_restants' => $fidelite->points_disponibles,
        ]);
    }
}
