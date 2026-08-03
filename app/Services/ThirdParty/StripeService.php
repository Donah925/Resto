<?php

namespace App\Services\ThirdParty;

use Stripe\StripeClient;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use App\Models\Order;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class StripeService
{
    protected StripeClient $stripe;
    protected string $webhookSecret;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret_key'));
        $this->webhookSecret = config('services.stripe.webhook_secret');
    }

    /**
     * Créer une session de paiement Checkout
     */
    public function createCheckoutSession(Order $order, User $user): array
    {
        try {
            $lineItems = [];
            
            foreach ($order->items as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $item->product->name,
                            'description' => $item->product->description,
                            'images' => $item->product->image ? [$item->product->image] : [],
                        ],
                        'unit_amount' => (int) ($item->unit_price * 100),
                    ],
                    'quantity' => $item->quantity,
                ];
            }

            // Frais de livraison
            if ($order->delivery_fee > 0) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Frais de livraison',
                        ],
                        'unit_amount' => (int) ($order->delivery_fee * 100),
                    ],
                    'quantity' => 1,
                ];
            }

            $session = $this->stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('client.orders.success', ['order' => $order->id, 'session_id' => '{CHECKOUT_SESSION_ID}']),
                'cancel_url' => route('client.orders.cancel', ['order' => $order->id]),
                'customer_email' => $user->email,
                'metadata' => [
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                ],
                'payment_intent_data' => [
                    'metadata' => [
                        'order_id' => $order->id,
                    ],
                ],
            ]);

            Log::info('Session Stripe créée', ['order_id' => $order->id, 'session_id' => $session->id]);

            return [
                'success' => true,
                'session_id' => $session->id,
                'url' => $session->url,
            ];
        } catch (Exception $e) {
            Log::error('Erreur création session Stripe', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Impossible de créer la session de paiement',
            ];
        }
    }

    /**
     * Vérifier le statut d'une session
     */
    public function getSessionStatus(string $sessionId): array
    {
        try {
            $session = $this->stripe->checkout->sessions->retrieve($sessionId);
            
            return [
                'success' => true,
                'status' => $session->payment_status,
                'paid' => $session->payment_status === 'paid',
            ];
        } catch (Exception $e) {
            Log::error('Erreur vérification session Stripe', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Impossible de vérifier le statut du paiement',
            ];
        }
    }

    /**
     * Traiter un webhook Stripe
     */
    public function handleWebhook(string $payload, string $signature): array
    {
        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                $this->webhookSecret
            );

            Log::info('Webhook Stripe reçu', ['type' => $event->type]);

            return match ($event->type) {
                'checkout.session.completed' => $this->handleCheckoutCompleted($event->data->object),
                'payment_intent.succeeded' => $this->handlePaymentSucceeded($event->data->object),
                'payment_intent.payment_failed' => $this->handlePaymentFailed($event->data->object),
                'charge.refunded' => $this->handleRefund($event->data->object),
                default => ['success' => true, 'message' => 'Webhook ignoré'],
            };
        } catch (Exception $e) {
            Log::error('Erreur traitement webhook Stripe', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Échec du traitement du webhook',
            ];
        }
    }

    protected function handleCheckoutCompleted($session): array
    {
        $orderId = $session->metadata->order_id ?? null;
        
        if (!$orderId) {
            return ['success' => false, 'error' => 'Order ID manquant'];
        }

        $order = Order::find($orderId);
        
        if (!$order) {
            return ['success' => false, 'error' => 'Commande non trouvée'];
        }

        // Mettre à jour la commande
        $order->update([
            'payment_status' => 'paid',
            'status' => 'confirmed',
            'stripe_session_id' => $session->id,
        ]);

        // Déclencher les notifications et jobs
        event(new \App\Events\OrderConfirmed($order));

        Log::info('Commande payée via Stripe', ['order_id' => $order->id]);

        return ['success' => true, 'order_id' => $order->id];
    }

    protected function handlePaymentSucceeded($paymentIntent): array
    {
        $orderId = $paymentIntent->metadata->order_id ?? null;
        
        if ($orderId) {
            Log::info('Paiement réussi', ['order_id' => $orderId]);
        }

        return ['success' => true];
    }

    protected function handlePaymentFailed($paymentIntent): array
    {
        $orderId = $paymentIntent->metadata->order_id ?? null;
        
        if ($orderId) {
            $order = Order::find($orderId);
            
            if ($order) {
                $order->update([
                    'payment_status' => 'failed',
                ]);

                Log::warning('Paiement échoué', ['order_id' => $order->id]);
            }
        }

        return ['success' => true];
    }

    protected function handleRefund($charge): array
    {
        // Gérer les remboursements
        Log::info('Remboursement traité', ['charge_id' => $charge->id]);

        return ['success' => true];
    }

    /**
     * Créer un remboursement
     */
    public function createRefund(Order $order, ?int $amount = null): array
    {
        try {
            if (!$order->stripe_payment_intent_id) {
                return ['success' => false, 'error' => 'Aucun paiement Stripe trouvé'];
            }

            $refundData = [
                'payment_intent' => $order->stripe_payment_intent_id,
            ];

            if ($amount !== null) {
                $refundData['amount'] = $amount;
            }

            $refund = $this->stripe->refunds->create($refundData);

            $order->update([
                'payment_status' => 'refunded',
                'refund_id' => $refund->id,
            ]);

            Log::info('Remboursement créé', ['order_id' => $order->id, 'refund_id' => $refund->id]);

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'amount' => $refund->amount / 100,
            ];
        } catch (Exception $e) {
            Log::error('Erreur remboursement Stripe', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Impossible de créer le remboursement',
            ];
        }
    }
}
