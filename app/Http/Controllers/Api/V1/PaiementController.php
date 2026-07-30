<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    public function creerIntent(Request $request)
    {
        $donnees = $request->validate([
            'montant' => 'required|numeric|min:1',
            'devise' => 'nullable|string|max:3',
            'commande_id' => 'nullable|exists:commandes,id',
        ]);

        $intent = $this->paymentService->creerIntentStripe(
            $donnees['montant'],
            $donnees['devise'] ?? 'XOF',
            $donnees['commande_id'] ?? null
        );

        return response()->json(['client_secret' => $intent->client_secret]);
    }

    public function initierMobileMoney(Request $request)
    {
        $donnees = $request->validate([
            'montant' => 'required|numeric|min:1',
            'telephone' => 'required|string',
            'operateur' => 'required|in:mtn,moov,orange_money,wave',
            'commande_id' => 'nullable|exists:commandes,id',
        ]);

        $paiement = $this->paymentService->initierPaiementMobileMoney($donnees);

        return response()->json([
            'message' => 'Paiement initié',
            'paiement' => $paiement,
        ]);
    }

    public function payerAvecPortefeuille(Request $request)
    {
        $donnees = $request->validate([
            'montant' => 'required|numeric|min:1',
            'commande_id' => 'nullable|exists:commandes,id',
        ]);

        $paiement = $this->paymentService->payerAvecPortefeuille(
            $request->user()->profilClient,
            $donnees['montant'],
            $donnees['commande_id'] ?? null
        );

        return response()->json([
            'message' => 'Paiement réussi',
            'paiement' => $paiement,
        ]);
    }

    public function payerAvecCarteCadeau(Request $request)
    {
        $donnees = $request->validate([
            'code_carte' => 'required|string',
            'montant' => 'required|numeric|min:1',
        ]);

        $paiement = $this->paymentService->payerAvecCarteCadeau(
            $request->user()->profilClient,
            $donnees['code_carte'],
            $donnees['montant']
        );

        return response()->json([
            'message' => 'Paiement réussi',
            'paiement' => $paiement,
        ]);
    }

    public function statut(Paiement $paiement, Request $request)
    {
        $this->autoriserAcces($paiement, $request->user());

        return response()->json(['paiement' => $paiement]);
    }

    private function autoriserAcces(Paiement $paiement, $user): void
    {
        if ($paiement->payable_type === 'App\\Models\\Commande') {
            if ($paiement->payable->client_id !== $user->profilClient->id) {
                abort(403);
            }
        }
    }
}
