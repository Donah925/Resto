<?php

namespace App\Services\Sms;

use Twilio\Rest\Client;
use Exception;
use Illuminate\Support\Facades\Log;

class TwilioSmsSender implements SmsSenderInterface
{
    protected Client $twilio;
    protected string $fromNumber;
    protected bool $enabled;

    public function __construct()
    {
        $this->enabled = config('sms.twilio.enabled', false);
        
        if ($this->enabled) {
            $this->twilio = new Client(
                config('sms.twilio.sid'),
                config('sms.twilio.token')
            );
            $this->fromNumber = config('sms.twilio.from_number');
        }
    }

    public function send(string $to, string $message): array
    {
        if (!$this->enabled) {
            Log::info('SMS non envoyé (Twilio désactivé)', ['to' => $to, 'message' => $message]);
            
            return [
                'success' => true,
                'message' => 'SMS simulé (Twilio désactivé)',
            ];
        }

        try {
            $messageObj = $this->twilio->messages->create($to, [
                'from' => $this->fromNumber,
                'body' => $message,
            ]);

            Log::info('SMS envoyé avec succès', [
                'to' => $to,
                'sid' => $messageObj->sid,
            ]);

            return [
                'success' => true,
                'sid' => $messageObj->sid,
                'status' => $messageObj->status,
            ];
        } catch (Exception $e) {
            Log::error('Erreur envoi SMS Twilio', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Impossible d\'envoyer le SMS',
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
        if (!$this->enabled) {
            return ['success' => true, 'status' => 'delivered'];
        }

        try {
            $message = $this->twilio->messages($messageId)->fetch();

            return [
                'success' => true,
                'status' => $message->status,
                'data' => $message,
            ];
        } catch (Exception $e) {
            Log::error('Erreur récupération statut SMS', [
                'sid' => $messageId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Impossible de récupérer le statut',
            ];
        }
    }

    public function getName(): string
    {
        return 'twilio';
    }
}
