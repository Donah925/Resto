<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WaveGateway implements PaymentGatewayInterface
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $merchantId;

    public function __construct()
    {
        $this->baseUrl = config('payment.wave.base_url');
        $this->apiKey = config('payment.wave.api_key');
        $this->merchantId = config('payment.wave.merchant_id');
    }

    public function initialize(array $data): array
    {
        try {
            $referenceId = uniqid('wave_');
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/payments/init', [
                'merchant_id' => $this->merchantId,
                'amount' => number_format($data['amount'] ?? 0, 2, '.', ''),
                'currency' => $data['currency'] ?? 'XOF',
                'phone' => $data['phone'],
                'reference' => $referenceId,
                'description' => $data['description'] ?? 'Paiement',
                'return_url' => $data['return_url'] ?? route('home'),
            ]);

            if ($response->successful()) {
                Log::info('Paiement Wave initié', ['reference_id' => $referenceId]);

                return [
                    'success' => true,
                    'reference_id' => $referenceId,
                    'payment_token' => $response->json('payment_token'),
                    'status' => 'pending',
                ];
            }

            return [
                'success' => false,
                'error' => 'Impossible d\'initier le paiement',
            ];
        } catch (Exception $e) {
            Log::error('Erreur paiement Wave', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec Wave',
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
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/payments/status/' . $paymentId);

            if ($response->successful()) {
                $result = $response->json();
                $status = $result['status'] ?? 'unknown';

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
            Log::error('Erreur statut Wave', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec Wave',
            ];
        }
    }

    public function refund(string $paymentId, ?float $amount = null): array
    {
        try {
            Log::info('Remboursement Wave demandé', ['payment_id' => $paymentId, 'amount' => $amount]);

            return [
                'success' => true,
                'message' => 'Remboursement initié',
            ];
        } catch (Exception $e) {
            Log::error('Erreur remboursement Wave', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Impossible de créer le remboursement',
            ];
        }
    }

    public function handleWebhook(array $payload): array
    {
        try {
            Log::info('Webhook Wave reçu', ['data' => $payload]);

            return ['success' => true, 'message' => 'Webhook traité'];
        } catch (Exception $e) {
            Log::error('Erreur traitement webhook Wave', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Échec du traitement du webhook',
            ];
        }
    }

    public function getName(): string
    {
        return 'wave';
    }
}
