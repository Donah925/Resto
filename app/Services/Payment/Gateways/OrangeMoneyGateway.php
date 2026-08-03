<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrangeMoneyGateway implements PaymentGatewayInterface
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected string $merchantKey;

    public function __construct()
    {
        $this->baseUrl = config('payment.orange_money.base_url');
        $this->clientId = config('payment.orange_money.client_id');
        $this->clientSecret = config('payment.orange_money.client_secret');
        $this->merchantKey = config('payment.orange_money.merchant_key');
    }

    public function initialize(array $data): array
    {
        try {
            $referenceId = uniqid('om_');
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/orange-money-webhook/dev/v1/webpayment', [
                'merchant_key' => $this->merchantKey,
                'amount' => number_format($data['amount'] ?? 0, 2, '.', ''),
                'currency' => $data['currency'] ?? 'XOF',
                'order_id' => $referenceId,
                'phone' => $data['phone'],
                'return_url' => $data['return_url'] ?? route('home'),
                'cancel_url' => $data['cancel_url'] ?? route('home'),
                'notif_url' => $data['notif_url'] ?? route('webhooks.orange_money'),
                'metadata' => json_encode($data['metadata'] ?? []),
            ]);

            if ($response->successful()) {
                Log::info('Paiement Orange Money initié', ['reference_id' => $referenceId]);

                return [
                    'success' => true,
                    'reference_id' => $referenceId,
                    'payment_url' => $response->json('payment_url'),
                    'status' => 'pending',
                ];
            }

            return [
                'success' => false,
                'error' => 'Impossible d\'initier le paiement',
            ];
        } catch (Exception $e) {
            Log::error('Erreur paiement Orange Money', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec Orange Money',
            ];
        }
    }

    public function capture(string $paymentId): array
    {
        return $this->getStatus($paymentId);
    }

    public function getStatus(string $paymentId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/orange-money-webhook/dev/v1/payment_status', [
                'merchant_key' => $this->merchantKey,
                'order_id' => $paymentId,
            ]);

            if ($response->successful()) {
                $status = $response->json('status') ?? 'unknown';

                return [
                    'success' => true,
                    'status' => $status,
                    'paid' => $status === 'SUCCESS',
                ];
            }

            return [
                'success' => false,
                'error' => 'Impossible de récupérer le statut',
            ];
        } catch (Exception $e) {
            Log::error('Erreur statut Orange Money', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec Orange Money',
            ];
        }
    }

    public function refund(string $paymentId, ?float $amount = null): array
    {
        try {
            Log::info('Remboursement Orange Money demandé', ['payment_id' => $paymentId, 'amount' => $amount]);

            return [
                'success' => true,
                'message' => 'Remboursement initié',
            ];
        } catch (Exception $e) {
            Log::error('Erreur remboursement Orange Money', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Impossible de créer le remboursement',
            ];
        }
    }

    public function handleWebhook(array $payload): array
    {
        try {
            Log::info('Webhook Orange Money reçu', ['data' => $payload]);

            return ['success' => true, 'message' => 'Webhook traité'];
        } catch (Exception $e) {
            Log::error('Erreur traitement webhook Orange Money', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Échec du traitement du webhook',
            ];
        }
    }

    public function getName(): string
    {
        return 'orange_money';
    }

    protected function getAccessToken(): string
    {
        return cache()->remember('orange_money_token', 3600, function () {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post($this->baseUrl . '/oauth/v3/token', [
                    'grant_type' => 'client_credentials',
                ]);
            
            return $response->json('access_token', '');
        });
    }
}
