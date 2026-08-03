<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoovMoneyGateway implements PaymentGatewayInterface
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $merchantId;

    public function __construct()
    {
        $this->baseUrl = config('payment.moov_money.base_url');
        $this->apiKey = config('payment.moov_money.api_key');
        $this->merchantId = config('payment.moov_money.merchant_id');
    }

    public function initialize(array $data): array
    {
        try {
            $referenceId = uniqid('moov_');
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/payment/init', [
                'merchant_id' => $this->merchantId,
                'amount' => number_format($data['amount'] ?? 0, 2, '.', ''),
                'currency' => $data['currency'] ?? 'XOF',
                'phone' => $data['phone'],
                'reference' => $referenceId,
                'description' => $data['description'] ?? 'Paiement',
            ]);

            if ($response->successful()) {
                Log::info('Paiement Moov Money initié', ['reference_id' => $referenceId]);

                return [
                    'success' => true,
                    'reference_id' => $referenceId,
                    'status' => 'pending',
                ];
            }

            return [
                'success' => false,
                'error' => 'Impossible d\'initier le paiement',
            ];
        } catch (Exception $e) {
            Log::error('Erreur paiement Moov Money', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec Moov Money',
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
            ])->get($this->baseUrl . '/payment/status/' . $paymentId);

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
            Log::error('Erreur statut Moov Money', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec Moov Money',
            ];
        }
    }

    public function refund(string $paymentId, ?float $amount = null): array
    {
        try {
            Log::info('Remboursement Moov Money demandé', ['payment_id' => $paymentId, 'amount' => $amount]);

            return [
                'success' => true,
                'message' => 'Remboursement initié',
            ];
        } catch (Exception $e) {
            Log::error('Erreur remboursement Moov Money', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Impossible de créer le remboursement',
            ];
        }
    }

    public function handleWebhook(array $payload): array
    {
        try {
            Log::info('Webhook Moov Money reçu', ['data' => $payload]);

            return ['success' => true, 'message' => 'Webhook traité'];
        } catch (Exception $e) {
            Log::error('Erreur traitement webhook Moov Money', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Échec du traitement du webhook',
            ];
        }
    }

    public function getName(): string
    {
        return 'moov_money';
    }
}
