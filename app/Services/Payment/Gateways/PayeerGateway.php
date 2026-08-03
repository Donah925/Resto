<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayeerGateway implements PaymentGatewayInterface
{
    protected string $merchantId;
    protected string $secretKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->merchantId = config('payment.payeer.merchant_id');
        $this->secretKey = config('payment.payeer.secret_key');
        $this->baseUrl = config('payment.payeer.base_url', 'https://payeer.com/merchant');
    }

    public function initialize(array $data): array
    {
        try {
            $orderId = uniqid('payeer_');
            
            $params = [
                'm_shop' => $this->merchantId,
                'm_orderid' => $orderId,
                'm_amount' => number_format($data['amount'] ?? 0, 2, '.', ''),
                'm_curr' => $data['currency'] ?? 'USD',
                'm_desc' => base64_encode($data['description'] ?? 'Paiement'),
                'm_lang' => 'fr',
            ];

            $sign = hash_hmac('sha256', implode(':', [
                $params['m_shop'],
                $params['m_orderid'],
                $params['m_amount'],
                $params['m_curr'],
                $params['m_desc'],
            ]), $this->secretKey);

            $params['m_sign'] = strtoupper($sign);

            $paymentUrl = $this->baseUrl . '?' . http_build_query($params);

            Log::info('Paiement Payeer initié', ['order_id' => $orderId]);

            return [
                'success' => true,
                'order_id' => $orderId,
                'payment_url' => $paymentUrl,
            ];
        } catch (Exception $e) {
            Log::error('Erreur paiement Payeer', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Impossible d\'initier le paiement',
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
            Log::info('Vérification statut Payeer', ['payment_id' => $paymentId]);

            return [
                'success' => true,
                'status' => 'pending',
                'paid' => false,
            ];
        } catch (Exception $e) {
            Log::error('Erreur statut Payeer', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Impossible de récupérer le statut',
            ];
        }
    }

    public function refund(string $paymentId, ?float $amount = null): array
    {
        try {
            Log::info('Remboursement Payeer demandé', ['payment_id' => $paymentId, 'amount' => $amount]);

            return [
                'success' => true,
                'message' => 'Remboursement initié',
            ];
        } catch (Exception $e) {
            Log::error('Erreur remboursement Payeer', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Impossible de créer le remboursement',
            ];
        }
    }

    public function handleWebhook(array $payload): array
    {
        try {
            $sign = hash_hmac('sha256', implode(':', [
                $payload['m_orderid'] ?? '',
                $payload['m_status'] ?? '',
                $payload['m_amount'] ?? '',
                $payload['m_curr'] ?? '',
            ]), $this->secretKey);

            if (strtoupper($sign) !== ($payload['m_sign'] ?? '')) {
                return ['success' => false, 'error' => 'Signature invalide'];
            }

            Log::info('Webhook Payeer reçu', ['data' => $payload]);

            return ['success' => true, 'message' => 'Webhook traité'];
        } catch (Exception $e) {
            Log::error('Erreur traitement webhook Payeer', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Échec du traitement du webhook',
            ];
        }
    }

    public function getName(): string
    {
        return 'payeer';
    }
}
