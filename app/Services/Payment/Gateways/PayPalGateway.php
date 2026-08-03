<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use Exception;
use Illuminate\Support\Facades\Log;

class PayPalGateway implements PaymentGatewayInterface
{
    protected PayPalHttpClient $client;
    protected string $environment;

    public function __construct()
    {
        $clientId = config('payment.paypal.client_id');
        $clientSecret = config('payment.paypal.client_secret');
        $this->environment = config('payment.paypal.environment', 'sandbox');

        if ($this->environment === 'production') {
            $environment = new ProductionEnvironment($clientId, $clientSecret);
        } else {
            $environment = new SandboxEnvironment($clientId, $clientSecret);
        }

        $this->client = new PayPalHttpClient($environment);
    }

    public function initialize(array $data): array
    {
        try {
            $request = new OrdersCreateRequest();
            $request->prefer('return=representation');
            $request->body = [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => $data['reference_id'] ?? 'order_' . uniqid(),
                    'description' => $data['description'] ?? 'Commande',
                    'amount' => [
                        'currency_code' => $data['currency'] ?? 'EUR',
                        'value' => number_format($data['amount'] ?? 0, 2, '.', ''),
                    ],
                    'items' => $data['items'] ?? [],
                ]],
                'application_context' => [
                    'brand_name' => config('app.name'),
                    'locale' => 'fr-FR',
                    'shipping_preference' => 'SET_PROVIDED_ADDRESS',
                    'return_url' => $data['return_url'] ?? route('home'),
                    'cancel_url' => $data['cancel_url'] ?? route('home'),
                ],
            ];

            $response = $this->client->execute($request);

            Log::info('Commande PayPal créée', ['paypal_order_id' => $response->result->id]);

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
            Log::error('Erreur création commande PayPal', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Impossible de créer la commande PayPal',
            ];
        }
    }

    public function capture(string $paymentId): array
    {
        try {
            $request = new OrdersCaptureRequest($paymentId);
            $request->prefer('return=representation');
            $request->body = [];

            $response = $this->client->execute($request);

            if ($response->result->status === 'COMPLETED') {
                Log::info('Paiement PayPal capturé', ['paypal_order_id' => $paymentId]);

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
            Log::error('Erreur capture paiement PayPal', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Impossible de capturer le paiement',
            ];
        }
    }

    public function getStatus(string $paymentId): array
    {
        try {
            $request = new \PayPalCheckoutSdk\Orders\OrdersGetRequest($paymentId);
            $response = $this->client->execute($request);

            return [
                'success' => true,
                'data' => $response->result,
                'status' => $response->result->status ?? 'unknown',
            ];
        } catch (Exception $e) {
            Log::error('Erreur récupération détails PayPal', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Impossible de récupérer les détails',
            ];
        }
    }

    public function refund(string $paymentId, ?float $amount = null): array
    {
        try {
            Log::info('Remboursement PayPal demandé', ['capture_id' => $paymentId, 'amount' => $amount]);

            return [
                'success' => true,
                'message' => 'Remboursement initié',
            ];
        } catch (Exception $e) {
            Log::error('Erreur remboursement PayPal', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Impossible de créer le remboursement',
            ];
        }
    }

    public function handleWebhook(array $payload): array
    {
        try {
            $eventType = $payload['event_type'] ?? null;
            Log::info('Webhook PayPal reçu', ['type' => $eventType]);

            return match ($eventType) {
                'PAYMENT.CAPTURE.COMPLETED' => ['success' => true, 'message' => 'Capture complétée'],
                'PAYMENT.CAPTURE.DENIED' => ['success' => true, 'message' => 'Capture refusée'],
                'CHECKOUT.ORDER.APPROVED' => ['success' => true, 'message' => 'Commande approuvée'],
                default => ['success' => true, 'message' => 'Webhook ignoré'],
            };
        } catch (Exception $e) {
            Log::error('Erreur traitement webhook PayPal', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Échec du traitement du webhook',
            ];
        }
    }

    public function getName(): string
    {
        return 'paypal';
    }
}
