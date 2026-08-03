<?php

namespace App\Services\Sms;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrangeSmsSender implements SmsSenderInterface
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected string $senderName;

    public function __construct()
    {
        $this->baseUrl = config('sms.orange.base_url');
        $this->clientId = config('sms.orange.client_id');
        $this->clientSecret = config('sms.orange.client_secret');
        $this->senderName = config('sms.orange.sender_name', 'OrangeSMS');
    }

    public function send(string $to, string $message): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/sms/send', [
                'from' => $this->senderName,
                'to' => $to,
                'text' => $message,
            ]);

            if ($response->successful()) {
                Log::info('SMS Orange envoyé avec succès', ['to' => $to]);

                return [
                    'success' => true,
                    'message_id' => $response->json('message_id'),
                    'status' => 'sent',
                ];
            }

            return [
                'success' => false,
                'error' => 'Impossible d\'envoyer le SMS',
            ];
        } catch (Exception $e) {
            Log::error('Erreur envoi SMS Orange', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec Orange SMS',
            ];
        }
    }

    public function sendVerificationCode(string $to, string $code): array
    {
        $message = sprintf(
            "%s - Code de vérification: %s\nNe partagez pas ce code.",
            config('app.name'),
            $code
        );

        return $this->send($to, $message);
    }

    public function getMessageStatus(string $messageId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
            ])->get($this->baseUrl . '/sms/status/' . $messageId);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => $response->json('status'),
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => 'Impossible de récupérer le statut',
            ];
        } catch (Exception $e) {
            Log::error('Erreur récupération statut SMS Orange', [
                'message_id' => $messageId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec Orange SMS',
            ];
        }
    }

    public function getName(): string
    {
        return 'orange_sms';
    }

    protected function getAccessToken(): string
    {
        return cache()->remember('orange_sms_token', 3600, function () {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post($this->baseUrl . '/oauth/token', [
                    'grant_type' => 'client_credentials',
                ]);
            
            return $response->json('access_token', '');
        });
    }
}
