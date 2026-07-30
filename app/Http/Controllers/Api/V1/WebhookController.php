<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    /**
     * Webhook Stripe
     */
    public function stripe(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook_secret')
            );

            $this->paymentService->traiterWebhookStripe($event);

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            \Log::error('Stripe webhook error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Webhook MTN Mobile Money
     */
    public function mtn(Request $request)
    {
        $this->verifierSignatureMtn($request);

        $this->paymentService->traiterWebhookMtn($request->all());

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Webhook Moov Money
     */
    public function moov(Request $request)
    {
        $this->paymentService->traiterWebhookMoov($request->all());
        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Webhook PayPal
     */
    public function paypal(Request $request)
    {
        $this->paymentService->traiterWebhookPaypal($request->all());
        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Webhook Orange Money
     */
    public function orangeMoney(Request $request)
    {
        $this->paymentService->traiterWebhookOrangeMoney($request->all());
        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Webhook Wave
     */
    public function wave(Request $request)
    {
        $this->paymentService->traiterWebhookWave($request->all());
        return response()->json(['status' => 'success'], 200);
    }

    private function verifierSignatureMtn(Request $request): void
    {
        // TODO: Implémenter vérification signature MTN
    }
}
