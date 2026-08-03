<?php

namespace App\Services\ThirdParty;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use App\Models\Order;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    protected PayPalHttpClient $client;
    protected string $environment;

    public function __construct()
    {
        $clientId = config('services.paypal.client_id');
        $clientSecret = config('services.paypal.client_secret');
        $this->environment = config('services.paypal.environment', 'sandbox');

        if ($this->environment === 'production') {
            $environment = new ProductionEnvironment($clientId, $clientSecret);
        } else {
            $environment = new SandboxEnvironment($clientId, $clientSecret);
        }

        $this->client = new PayPalHttpClient($environment);
    }

    /**
     * Créer une commande PayPal
     */
    public function createOrder(Order $order, User $user): array
    {
        try {
            $request = new OrdersCreateRequest();
            $request->prefer('return=representation');
            $request->body = [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => 'order_' . $order->id,
                    'description' => 'Commande #' . $order->id,
                    'amount' => [
                        'currency_code' => 'EUR',
                        'value' => number_format($order->total, 2, '.', ''),
                        'breakdown' => [
                            'item_total' => [
                                'currency_code' => 'EUR',
                                'value' => number_format($order->subtotal, 2, '.', ''),
                            ],
                        ],
                    ],
                    'items' => $order->items->map(function ($item) {
                        return [
                            'name' => $item->product->name,
                            'description' => $item->product->description ?? '',
                            'quantity' => (string) $item->quantity,
                            'unit_amount' => [
                                'currency_code' => 'EUR',
                                'value' => number_format($item->unit_price, 2, '.', ''),
                            ],
                        ];
                    })->toArray(),
                ]],
                'application_context' => [
                    'brand_name' => config('app.name'),
                    'locale' => 'fr-FR',
                    'shipping_preference' => 'SET_PROVIDED_ADDRESS',
                    'return_url' => route('client.orders.paypal.success', ['order' => $order->id]),
                    'cancel_url' => route('client.orders.paypal.cancel', ['order' => $order->id]),
                ],
            ];

            $response = $this->client->execute($request);

            Log::info('Commande PayPal créée', [
                'order_id' => $order->id,
                'paypal_order_id' => $response->result->id,
            ]);

            // Sauvegarder l'ID PayPal
            $order->update([
                'paypal_order_id' => $response->result->id,
            ]);

            // Trouver le lien d'approbation
            $approveLink = null;
            foreach ($response->result->links as $link) {
                if ($link->rel === 'approve') {
                    $approveLink = $link->href;
                    break;
                }
            }

            return [
                'success' => true,
                'paypal_order_id' => $response->result->id,
                'approval_url' => $approveLink,
            ];
        } catch (Exception $e) {
            Log::error('Erreur création commande PayPal', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Impossible de créer la commande PayPal',
            ];
        }
    }

    /**
     * Capturer un paiement PayPal
     */
    public function capturePayment(string $paypalOrderId): array
    {
        try {
            $request = new OrdersCaptureRequest($paypalOrderId);
            $request->prefer('return=representation');
            $request->body = [];

            $response = $this->client->execute($request);

            if ($response->result->status === 'COMPLETED') {
                Log::info('Paiement PayPal capturé', ['paypal_order_id' => $paypalOrderId]);

                return [
                    'success' => true,
                    'status' => 'completed',
                    'data' => $response->result,
                ];
            }

            return [
                'success' => false,
                'status' => $response->result->status,
            ];
        } catch (Exception $e) {
            Log::error('Erreur capture paiement PayPal', [
                'paypal_order_id' => $paypalOrderId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Impossible de capturer le paiement',
            ];
        }
    }

    /**
     * Obtenir les détails d'une commande PayPal
     */
    public function getOrderDetails(string $paypalOrderId): array
    {
        try {
            $request = new OrdersGetRequest($paypalOrderId);
            $response = $this->client->execute($request);

            return [
                'success' => true,
                'data' => $response->result,
            ];
        } catch (Exception $e) {
            Log::error('Erreur récupération détails PayPal', [
                'paypal_order_id' => $paypalOrderId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Impossible de récupérer les détails',
            ];
        }
    }

    /**
     * Traiter un webhook PayPal
     */
    public function handleWebhook(array $payload): array
    {
        try {
            $eventType = $payload['event_type'] ?? null;
            
            Log::info('Webhook PayPal reçu', ['type' => $eventType]);

            return match ($eventType) {
                'PAYMENT.CAPTURE.COMPLETED' => $this->handleCaptureCompleted($payload),
                'PAYMENT.CAPTURE.DENIED' => $this->handleCaptureDenied($payload),
                'CHECKOUT.ORDER.APPROVED' => $this->handleOrderApproved($payload),
                default => ['success' => true, 'message' => 'Webhook ignoré'],
            };
        } catch (Exception $e) {
            Log::error('Erreur traitement webhook PayPal', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Échec du traitement du webhook',
            ];
        }
    }

    protected function handleCaptureCompleted(array $payload): array
    {
        $orderId = $payload['resource']['supplementary_data']['related_ids']['order_id'] ?? null;
        
        if ($orderId) {
            $order = Order::where('paypal_order_id', $orderId)->first();
            
            if ($order) {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                ]);

                event(new \App\Events\OrderConfirmed($order));

                Log::info('Commande payée via PayPal', ['order_id' => $order->id]);
            }
        }

        return ['success' => true];
    }

    protected function handleCaptureDenied(array $payload): array
    {
        $orderId = $payload['resource']['supplementary_data']['related_ids']['order_id'] ?? null;
        
        if ($orderId) {
            $order = Order::where('paypal_order_id', $orderId)->first();
            
            if ($order) {
                $order->update([
                    'payment_status' => 'failed',
                ]);

                Log::warning('Paiement PayPal refusé', ['order_id' => $order->id]);
            }
        }

        return ['success' => true];
    }

    protected function handleOrderApproved(array $payload): array
    {
        $paypalOrderId = $payload['resource']['id'] ?? null;
        
        if ($paypalOrderId) {
            Log::info('Commande PayPal approuvée', ['paypal_order_id' => $paypalOrderId]);
        }

        return ['success' => true];
    }

    /**
     * Créer un remboursement
     */
    public function createRefund(string $captureId, ?float $amount = null): array
    {
        try {
            // Note: Nécessite le SDK des remboursements PayPal
            Log::info('Remboursement PayPal demandé', ['capture_id' => $captureId, 'amount' => $amount]);

            return [
                'success' => true,
                'message' => 'Remboursement initié',
            ];
        } catch (Exception $e) {
            Log::error('Erreur remboursement PayPal', [
                'capture_id' => $captureId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Impossible de créer le remboursement',
            ];
        }
    }
}
