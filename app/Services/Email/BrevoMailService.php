<?php

namespace App\Services\Email;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class BrevoMailService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $fromEmail;
    protected string $fromName;

    public function __construct()
    {
        $this->apiKey = config('services.brevo.api_key');
        $this->baseUrl = 'https://api.brevo.com/v3';
        $this->fromEmail = config('mail.from.address', 'noreply@example.com');
        $this->fromName = config('mail.from.name', config('app.name'));
    }

    /**
     * Envoyer un email
     *
     * @param string $to Email du destinataire
     * @param string $subject Sujet de l'email
     * @param string $content Contenu HTML de l'email
     * @param array $options Options supplémentaires
     * @return array Résultat de l'envoi
     */
    public function send(string $to, string $subject, string $content, array $options = []): array
    {
        try {
            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/smtp/email', [
                'sender' => [
                    'email' => $this->fromEmail,
                    'name' => $this->fromName,
                ],
                'to' => [
                    ['email' => $to],
                ],
                'subject' => $subject,
                'htmlContent' => $content,
                'replyTo' => $options['reply_to'] ?? null,
                'cc' => $options['cc'] ?? [],
                'bcc' => $options['bcc'] ?? [],
                'attachment' => $options['attachments'] ?? [],
                'tags' => $options['tags'] ?? [],
            ]);

            if ($response->successful()) {
                Log::info('Email Brevo envoyé avec succès', ['to' => $to, 'subject' => $subject]);

                return [
                    'success' => true,
                    'message_id' => $response->json('messageId'),
                ];
            }

            return [
                'success' => false,
                'error' => 'Impossible d\'envoyer l\'email',
            ];
        } catch (Exception $e) {
            Log::error('Erreur envoi email Brevo', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec Brevo',
            ];
        }
    }

    /**
     * Envoyer un email transactionnel
     *
     * @param string $to Email du destinataire
     * @param string $templateId ID du template
     * @param array $params Paramètres du template
     * @return array Résultat de l'envoi
     */
    public function sendTransactional(string $to, int $templateId, array $params = []): array
    {
        try {
            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/smtp/email', [
                'sender' => [
                    'email' => $this->fromEmail,
                    'name' => $this->fromName,
                ],
                'to' => [
                    ['email' => $to],
                ],
                'templateId' => $templateId,
                'params' => $params,
            ]);

            if ($response->successful()) {
                Log::info('Email transactionnel Brevo envoyé', ['to' => $to, 'template_id' => $templateId]);

                return [
                    'success' => true,
                    'message_id' => $response->json('messageId'),
                ];
            }

            return [
                'success' => false,
                'error' => 'Impossible d\'envoyer l\'email transactionnel',
            ];
        } catch (Exception $e) {
            Log::error('Erreur envoi email transactionnel Brevo', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec Brevo',
            ];
        }
    }

    /**
     * Ajouter un contact à une liste
     *
     * @param string $email Email du contact
     * @param array $attributes Attributs du contact
     * @param int|null $listId ID de la liste
     * @return array Résultat de l'ajout
     */
    public function addContact(string $email, array $attributes = [], ?int $listId = null): array
    {
        try {
            $data = [
                'email' => $email,
                'attributes' => $attributes,
                'updateEnabled' => true,
            ];

            if ($listId !== null) {
                $data['listIds'] = [$listId];
            }

            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/contacts', $data);

            if ($response->successful()) {
                Log::info('Contact Brevo ajouté', ['email' => $email]);

                return [
                    'success' => true,
                    'contact_id' => $response->json('id'),
                ];
            }

            return [
                'success' => false,
                'error' => 'Impossible d\'ajouter le contact',
            ];
        } catch (Exception $e) {
            Log::error('Erreur ajout contact Brevo', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec Brevo',
            ];
        }
    }
}
