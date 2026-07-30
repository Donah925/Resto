<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ParrainageController extends Controller
{
    public function show(Request $request)
    {
        $client = $request->user()->profilClient;

        return response()->json([
            'code_parrainage' => $client->code_parrainage,
            'parrainages' => $client->parrainages()->count(),
            'bonus_gagnes' => $client->parrainages()->sum('bonus_parrain'),
        ]);
    }

    public function valider(Request $request)
    {
        $request->validate([
            'code_parrainage' => 'required|string|exists:profils_client,code_parrainage',
        ]);

        // TODO: Valider le code de parrainage pour un filleul
        return response()->json(['message' => 'À implémenter'], 501);
    }
}
