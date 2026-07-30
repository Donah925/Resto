<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PortefeuilleController extends Controller
{
    public function show(Request $request)
    {
        $portefeuille = $request->user()->profilClient->portefeuille;

        return response()->json(['portefeuille' => $portefeuille]);
    }

    public function recharger(Request $request)
    {
        $donnees = $request->validate([
            'montant' => 'required|numeric|min:100',
            'methode_paiement' => 'required|in:carte_bancaire,mtn_mobile_money,moov_money,orange_money,wave',
            'telephone' => 'nullable|string|max:20',
        ]);

        // TODO: Implémenter le paiement via PaymentService
        $paiement = null; // $this->paymentService->rechargerPortefeuille(...)

        return response()->json([
            'message' => 'Rechargement initié',
            'paiement' => $paiement,
        ]);
    }

    public function transactions(Request $request)
    {
        $transactions = $request->user()->profilClient->portefeuille
            ->transactions()
            ->latest()
            ->paginate($request->input('per_page', 20));

        return response()->json(['transactions' => $transactions]);
    }
}
