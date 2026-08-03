<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\ThirdParty\StripeService;
use App\Services\ThirdParty\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function __construct(
        protected StripeService $stripeService,
        protected PayPalService $payPalService
    ) {}

    /**
     * Webhook Stripe
     */
    public function stripe(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        if (!$signature) {
            Log::warning('Webhook Stripe sans signature');
            return response()->json(['error' => 'Signature manquante'], 400);
        }

        $result = $this->stripeService->handleWebhook($payload, $signature);

        if ($result['success']) {
            return response()->json(['received' => true]);
        }

        return response()->json(['error' => $result['error']], 400);
    }

    /**
     * Webhook PayPal
     */
    public function paypal(Request $request)
    {
        $payload = $request->all();
        
        // Vérifier l'ID de transmission pour la sécurité en production
        $transmissionId = $request->header('Paypal-Transmission-Id');
        
        if (!$transmissionId) {
            Log::warning('Webhook PayPal sans ID de transmission');
        }

        $result = $this->payPalService->handleWebhook($payload);

        if ($result['success']) {
            return response()->json(['received' => true]);
        }

        return response()->json(['error' => $result['error']], 400);
    }
}
