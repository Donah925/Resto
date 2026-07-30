<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CarteCadeauController extends Controller
{
    public function acheter(Request $request)
    {
        $donnees = $request->validate([
            'montant' => 'required|numeric|min:1000',
            'destinataire_email' => 'nullable|email|max:255',
            'message' => 'nullable|string|max:500',
            'methode_paiement' => 'required|in:carte_bancaire,mtn_mobile_money,moov_money,orange_money,wave',
        ]);

        // TODO: Implémenter l'achat de carte cadeau
        return response()->json(['message' => 'À implémenter'], 501);
    }

    public function utiliser(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        // TODO: Implémenter l'utilisation de carte cadeau
        return response()->json(['message' => 'À implémenter'], 501);
    }

    public function mesCartes(Request $request)
    {
        // TODO: Retourner les cartes cadeaux de l'utilisateur
        return response()->json(['cartes' => []]);
    }
}
