<?php

namespace App\Services\ThirdParty;

use Twilio\Rest\Client;
use App\Models\User;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    protected Client $twilio;
    protected string $fromNumber;
    protected bool $enabled;

    public function __construct()
    {
        $this->enabled = config('services.twilio.enabled', false);
        
        if ($this->enabled) {
            $this->twilio = new Client(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );
            $this->fromNumber = config('services.twilio.from_number');
        }
    }

    /**
     * Envoyer un SMS
     */
    public function sendSms(string $to, string $message): array
    {
        if (!$this->enabled) {
            Log::info('SMS non envoyé (Twilio désactivé)', ['to' => $to, 'message' => $message]);
            
            return [
                'success' => true,
                'message' => 'SMS simulé (Twilio désactivé)',
            ];
        }

        try {
            $message = $this->twilio->messages->create($to, [
                'from' => $this->fromNumber,
                'body' => $message,
            ]);

            Log::info('SMS envoyé avec succès', [
                'to' => $to,
                'sid' => $message->sid,
            ]);

            return [
                'success' => true,
                'sid' => $message->sid,
                'status' => $message->status,
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

    /**
     * Envoyer une notification de commande
     */
    public function sendOrderNotification(Order $order, User $user): array
    {
        $message = sprintf(
            "🍽️ %s - Commande #%s\n" .
            "Statut: %s\n" .
            "Total: %.2f€\n" .
            "Livraison: %s",
            config('app.name'),
            $order->id,
            $this->getStatusLabel($order->status),
            $order->total,
            $order->delivery_address ?? 'À emporter'
        );

        return $this->sendSms($user->phone, $message);
    }

    /**
     * Envoyer une notification de livraison
     */
    public function sendDeliveryUpdate(Order $order, User $user): array
    {
        $message = sprintf(
            "🚚 Livraison en cours\n" .
            "Commande #%s\n" .
            "Votre livreur est en route !",
            $order->id
        );

        return $this->sendSms($user->phone, $message);
    }

    /**
     * Envoyer un code de vérification OTP
     */
    public function sendVerificationCode(string $phone, string $code): array
    {
        $message = sprintf(
            "%s - Code de vérification: %s\n" .
            "Ne partagez pas ce code.",
            config('app.name'),
            $code
        );

        return $this->sendSms($phone, $message);
    }

    /**
     * Envoyer une notification de réservation
     */
    public function sendReservationReminder(User $user, \App\Models\Reservation $reservation): array
    {
        $message = sprintf(
            "📅 Rappel de réservation\n" .
            "%s\n" .
            "Date: %s à %s\n" .
            "Personnes: %d",
            $reservation->restaurant->name,
            $reservation->date->format('d/m/Y'),
            $reservation->time,
            $reservation->guest_count
        );

        return $this->sendSms($user->phone, $message);
    }

    /**
     * Obtenir le statut d'un message
     */
    public function getMessageStatus(string $messageSid): array
    {
        if (!$this->enabled) {
            return ['success' => true, 'status' => 'delivered'];
        }

        try {
            $message = $this->twilio->messages($messageSid)->fetch();

            return [
                'success' => true,
                'status' => $message->status,
                'data' => $message,
            ];
        } catch (Exception $e) {
            Log::error('Erreur récupération statut SMS', [
                'sid' => $messageSid,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Impossible de récupérer le statut',
            ];
        }
    }

    protected function getStatusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'preparing' => 'En préparation',
            'ready' => 'Prête',
            'on_the_way' => 'En livraison',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée',
            default => $status,
        };
    }
}
