<?php

namespace App\Services\ThirdParty;

use SendGrid;
use SendGrid\Mail\Mail;
use SendGrid\Mail\TypeTo;
use SendGrid\Mail\Subject;
use SendGrid\Mail\Content;
use App\Models\User;
use App\Models\Order;
use App\Models\Reservation;
use Exception;
use Illuminate\Support\Facades\Log;

class SendGridService
{
    protected SendGrid $sendgrid;
    protected string $fromEmail;
    protected string $fromName;
    protected bool $enabled;

    public function __construct()
    {
        $this->enabled = config('services.sendgrid.enabled', false);
        
        if ($this->enabled) {
            $this->sendgrid = new SendGrid(config('services.sendgrid.api_key'));
            $this->fromEmail = config('services.sendgrid.from_email');
            $this->fromName = config('services.sendgrid.from_name', config('app.name'));
        }
    }

    /**
     * Envoyer un email
     */
    public function sendEmail(
        string $to,
        string $subject,
        string $htmlContent,
        ?string $textContent = null,
        array $dynamicData = []
    ): array {
        if (!$this->enabled) {
            Log::info('Email non envoyé (SendGrid désactivé)', [
                'to' => $to,
                'subject' => $subject,
            ]);
            
            return [
                'success' => true,
                'message' => 'Email simulé (SendGrid désactivé)',
            ];
        }

        try {
            $email = new Mail();
            $email->setFrom($this->fromEmail, $this->fromName);
            $email->addTo($to);
            $email->setSubject($subject);
            $email->addContent('text/html', $htmlContent);
            
            if ($textContent) {
                $email->addContent('text/plain', $textContent);
            }

            // Données dynamiques pour les templates
            if (!empty($dynamicData)) {
                $email->setTemplateDynamicData($dynamicData);
            }

            $response = $this->sendgrid->send($email);

            Log::info('Email envoyé avec succès', [
                'to' => $to,
                'subject' => $subject,
                'status_code' => $response->statusCode(),
            ]);

            return [
                'success' => true,
                'status_code' => $response->statusCode(),
                'headers' => $response->headers(),
            ];
        } catch (Exception $e) {
            Log::error('Erreur envoi email SendGrid', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Impossible d\'envoyer l\'email',
            ];
        }
    }

    /**
     * Envoyer une confirmation de commande
     */
    public function sendOrderConfirmation(Order $order): array
    {
        $subject = sprintf('Confirmation de commande #%s', $order->id);
        
        $htmlContent = view('emails.orders.confirmation', compact('order'))->render();

        return $this->sendEmail(
            $order->user->email,
            $subject,
            $htmlContent,
            null,
            [
                'order_id' => $order->id,
                'total' => $order->total,
                'restaurant_name' => $order->restaurant->name,
            ]
        );
    }

    /**
     * Envoyer une notification de statut de commande
     */
    public function sendOrderStatusUpdate(Order $order): array
    {
        $subject = sprintf('Statut de votre commande #%s', $order->id);
        
        $htmlContent = view('emails.orders.status-update', compact('order'))->render();

        return $this->sendEmail(
            $order->user->email,
            $subject,
            $htmlContent,
            null,
            [
                'order_id' => $order->id,
                'status' => $this->getStatusLabel($order->status),
            ]
        );
    }

    /**
     * Envoyer une confirmation de réservation
     */
    public function sendReservationConfirmation(Reservation $reservation): array
    {
        $subject = 'Confirmation de votre réservation';
        
        $htmlContent = view('emails.reservations.confirmation', compact('reservation'))->render();

        return $this->sendEmail(
            $reservation->user->email,
            $subject,
            $htmlContent,
            null,
            [
                'restaurant_name' => $reservation->restaurant->name,
                'date' => $reservation->date->format('d/m/Y'),
                'time' => $reservation->time,
                'guest_count' => $reservation->guest_count,
            ]
        );
    }

    /**
     * Envoyer un rappel de réservation
     */
    public function sendReservationReminder(Reservation $reservation): array
    {
        $subject = 'Rappel de votre réservation';
        
        $htmlContent = view('emails.reservations.reminder', compact('reservation'))->render();

        return $this->sendEmail(
            $reservation->user->email,
            $subject,
            $htmlContent,
            null,
            [
                'restaurant_name' => $reservation->restaurant->name,
                'date' => $reservation->date->format('d/m/Y'),
                'time' => $reservation->time,
            ]
        );
    }

    /**
     * Envoyer une réinitialisation de mot de passe
     */
    public function sendPasswordReset(User $user, string $token): array
    {
        $subject = 'Réinitialisation de votre mot de passe';
        
        $htmlContent = view('emails.auth.password-reset', [
            'user' => $user,
            'token' => $token,
            'url' => route('password.reset', ['token' => $token, 'email' => $user->email]),
        ])->render();

        return $this->sendEmail(
            $user->email,
            $subject,
            $htmlContent,
            'Cliquez sur le lien suivant pour réinitialiser votre mot de passe: ' . route('password.reset', ['token' => $token, 'email' => $user->email])
        );
    }

    /**
     * Envoyer une vérification d'email
     */
    public function sendEmailVerification(User $user, string $url): array
    {
        $subject = 'Vérifiez votre adresse email';
        
        $htmlContent = view('emails.auth.verify-email', [
            'user' => $user,
            'url' => $url,
        ])->render();

        return $this->sendEmail(
            $user->email,
            $subject,
            $htmlContent,
            'Cliquez sur le lien suivant pour vérifier votre email: ' . $url
        );
    }

    /**
     * Envoyer une newsletter
     */
    public function sendNewsletter(User $user, string $subject, string $content): array
    {
        return $this->sendEmail(
            $user->email,
            $subject,
            $content,
            null,
            ['user_name' => $user->name]
        );
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
