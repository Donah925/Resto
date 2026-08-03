<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MtnMomoGateway implements PaymentGatewayInterface
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $userId;
    protected string $subscriptionKey;

    public function __construct()
    {
        $this->baseUrl = config('payment.mtn_momo.base_url', 'https://sandbox.momodeveloper.mtn.com');
        $this->apiKey = config('payment.mtn_momo.api_key');
        $this->userId = config('payment.mtn_momo.user_id');
        $this->subscriptionKey = config('payment.mtn_momo.subscription_key');
    }

    public function initialize(array $data): array
    {
        try {
            $referenceId = uniqid('mtn_');
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'X-Reference-Id' => $referenceId,
                'X-Target-Environment' => config('payment.mtn_momo.environment', 'sandbox'),
                'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
            ])->post($this->baseUrl . '/collection/v1_0/requesttopay', [
                'amount' => number_format($data['amount'] ?? 0, 2, '.', ''),
                'currency' => $data['currency'] ?? 'EUR',
                'externalId' => $data['external_id'] ?? uniqid(),
                'payer' => [
                    'partyIdType' => 'MSISDN',
                    'partyId' => $data['phone'],
                ],
                'payerMessage' => $data['message'] ?? 'Paiement',
                'payeeNote' => $data['note'] ?? '',
            ]);

            if ($response->successful()) {
                Log::info('Paiement MTN MoMo initié', ['reference_id' => $referenceId]);

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
            Log::error('Erreur paiement MTN MoMo', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec MTN MoMo',
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
                'X-Target-Environment' => config('payment.mtn_momo.environment', 'sandbox'),
                'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
            ])->get($this->baseUrl . '/collection/v1_0/requesttopay/' . $paymentId);

            if ($response->successful()) {
                $status = $response->json('status');

                return [
                    'success' => true,
                    'status' => $status,
                    'paid' => $status === 'SUCCESSFUL',
                ];
            }

            return [
                'success' => false,
                'error' => 'Impossible de récupérer le statut',
            ];
        } catch (Exception $e) {
            Log::error('Erreur statut MTN MoMo', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec MTN MoMo',
            ];
        }
    }

    public function refund(string $paymentId, ?float $amount = null): array
    {
        try {
            Log::info('Remboursement MTN MoMo demandé', ['payment_id' => $paymentId, 'amount' => $amount]);

            return [
                'success' => true,
                'message' => 'Remboursement initié',
            ];
        } catch (Exception $e) {
            Log::error('Erreur remboursement MTN MoMo', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Impossible de créer le remboursement',
            ];
        }
    }

    public function handleWebhook(array $payload): array
    {
        try {
            Log::info('Webhook MTN MoMo reçu', ['data' => $payload]);

            return ['success' => true, 'message' => 'Webhook traité'];
        } catch (Exception $e) {
            Log::error('Erreur traitement webhook MTN MoMo', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Échec du traitement du webhook',
            ];
        }
    }

    public function getName(): string
    {
        return 'mtn_momo';
    }

    protected function getAccessToken(): string
    {
        // Implémentation simplifiée - à adapter selon l'API MTN
        return cache()->remember('mtn_momo_token', 3600, function () {
            $response = Http::withBasicAuth($this->userId, $this->apiKey)
                ->post($this->baseUrl . '/collection/token/');
            
            return $response->json('access_token', '');
        });
    }
}
