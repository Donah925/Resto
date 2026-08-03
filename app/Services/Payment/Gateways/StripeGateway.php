<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Stripe\StripeClient;
use Stripe\Checkout\Session;
use Exception;
use Illuminate\Support\Facades\Log;

class StripeGateway implements PaymentGatewayInterface
{
    protected StripeClient $stripe;
    protected string $webhookSecret;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('payment.stripe.secret_key'));
        $this->webhookSecret = config('payment.stripe.webhook_secret');
    }

    public function initialize(array $data): array
    {
        try {
            $lineItems = $data['line_items'] ?? [];
            
            $session = $this->stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => $data['success_url'] ?? route('home'),
                'cancel_url' => $data['cancel_url'] ?? route('home'),
                'customer_email' => $data['customer_email'] ?? null,
                'metadata' => $data['metadata'] ?? [],
            ]);

            Log::info('Session Stripe créée', ['session_id' => $session->id]);

            return [
                'success' => true,
                'session_id' => $session->id,
                'url' => $session->url,
            ];
        } catch (Exception $e) {
            Log::error('Erreur création session Stripe', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Impossible de créer la session de paiement',
            ];
        }
    }

    public function capture(string $paymentId): array
    {
        try {
            $session = $this->stripe->checkout->sessions->retrieve($paymentId);
            
            return [
                'success' => true,
                'status' => $session->payment_status,
                'paid' => $session->payment_status === 'paid',
            ];
        } catch (Exception $e) {
            Log::error('Erreur vérification session Stripe', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Impossible de vérifier le statut du paiement',
            ];
        }
    }

    public function getStatus(string $paymentId): array
    {
        return $this->capture($paymentId);
    }

    public function refund(string $paymentId, ?float $amount = null): array
    {
        try {
            $refundData = ['payment_intent' => $paymentId];

            if ($amount !== null) {
                $refundData['amount'] = (int) ($amount * 100);
            }

            $refund = $this->stripe->refunds->create($refundData);

            Log::info('Remboursement créé', ['refund_id' => $refund->id]);

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'amount' => $refund->amount / 100,
            ];
        } catch (Exception $e) {
            Log::error('Erreur remboursement Stripe', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Impossible de créer le remboursement',
            ];
        }
    }

    public function handleWebhook(array $payload): array
    {
        try {
            $eventType = $payload['type'] ?? null;
            Log::info('Webhook Stripe reçu', ['type' => $eventType]);

            return match ($eventType) {
                'checkout.session.completed' => ['success' => true, 'message' => 'Session complétée'],
                'payment_intent.succeeded' => ['success' => true, 'message' => 'Paiement réussi'],
                'payment_intent.payment_failed' => ['success' => true, 'message' => 'Paiement échoué'],
                'charge.refunded' => ['success' => true, 'message' => 'Remboursement traité'],
                default => ['success' => true, 'message' => 'Webhook ignoré'],
            };
        } catch (Exception $e) {
            Log::error('Erreur traitement webhook Stripe', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Échec du traitement du webhook',
            ];
        }
    }

    public function getName(): string
    {
        return 'stripe';
    }
}
