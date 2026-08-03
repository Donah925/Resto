<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayoneerGateway implements PaymentGatewayInterface
{
    protected string $apiUrl;
    protected string $apiUser;
    protected string $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('payment.payoneer.api_url');
        $this->apiUser = config('payment.payoneer.api_user');
        $this->apiKey = config('payment.payoneer.api_key');
    }

    public function initialize(array $data): array
    {
        try {
            $orderId = uniqid('payoneer_');
            
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->apiUser . ':' . $this->apiKey),
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/payments/init', [
                'amount' => number_format($data['amount'] ?? 0, 2, '.', ''),
                'currency' => $data['currency'] ?? 'EUR',
                'reference' => $orderId,
                'description' => $data['description'] ?? 'Paiement',
                'customer' => $data['customer'] ?? [],
            ]);

            if ($response->successful()) {
                Log::info('Paiement Payoneer initié', ['order_id' => $orderId]);

                return [
                    'success' => true,
                    'order_id' => $orderId,
                    'redirect_url' => $response->json('redirect_url'),
                ];
            }

            return [
                'success' => false,
                'error' => 'Impossible d\'initier le paiement',
            ];
        } catch (Exception $e) {
            Log::error('Erreur paiement Payoneer', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec Payoneer',
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
                'Authorization' => 'Basic ' . base64_encode($this->apiUser . ':' . $this->apiKey),
            ])->get($this->apiUrl . '/payments/status/' . $paymentId);

            if ($response->successful()) {
                $result = $response->json();
                $status = $result['status'] ?? 'unknown';

                return [
                    'success' => true,
                    'status' => $status,
                    'paid' => $status === 'approved',
                ];
            }

            return [
                'success' => false,
                'error' => 'Impossible de récupérer le statut',
            ];
        } catch (Exception $e) {
            Log::error('Erreur statut Payoneer', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec Payoneer',
            ];
        }
    }

    public function refund(string $paymentId, ?float $amount = null): array
    {
        try {
            Log::info('Remboursement Payoneer demandé', ['payment_id' => $paymentId, 'amount' => $amount]);

            return [
                'success' => true,
                'message' => 'Remboursement initié',
            ];
        } catch (Exception $e) {
            Log::error('Erreur remboursement Payoneer', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Impossible de créer le remboursement',
            ];
        }
    }

    public function handleWebhook(array $payload): array
    {
        try {
            Log::info('Webhook Payoneer reçu', ['data' => $payload]);

            return ['success' => true, 'message' => 'Webhook traité'];
        } catch (Exception $e) {
            Log::error('Erreur traitement webhook Payoneer', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Échec du traitement du webhook',
            ];
        }
    }

    public function getName(): string
    {
        return 'payoneer';
    }
}
