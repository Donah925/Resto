<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Envoyer une notification push à un utilisateur
     */
    public function envoyerPush(User $user, string $titre, string $message, array $data = []): void
    {
        try {
            // Utiliser Firebase Cloud Messaging ou service similaire
            $tokens = $user->deviceTokens()->pluck('token')->toArray();

            if (empty($tokens)) {
                Log::info("Aucun token FCM pour l'utilisateur {$user->id}");
                return;
            }

            $payload = [
                'to' => $tokens,
                'notification' => [
                    'title' => $titre,
                    'body' => $message,
                    'sound' => 'default',
                    'badge' => '1',
                ],
                'data' => $data,
            ];

            // Appel API FCM (à implémenter avec votre configuration)
            // Http::withHeaders([...])->post('https://fcm.googleapis.com/fcm/send', $payload);

            // Créer la notification en base de données
            $this->creerNotification($user, $titre, $message, 'push', $data);

        } catch (\Exception $e) {
            Log::error("Erreur envoi notification push: " . $e->getMessage());
        }
    }

    /**
     * Envoyer une notification par email
     */
    public function envoyerEmail(User $user, string $sujet, string $vue, array $donnees = []): void
    {
        try {
            Mail::send($vue, $donnees, function ($message) use ($user, $sujet) {
                $message->to($user->email)
                        ->subject($sujet);
            });

            $this->creerNotification($user, $sujet, 'Email envoyé', 'email', ['sujet' => $sujet]);

        } catch (\Exception $e) {
            Log::error("Erreur envoi email: " . $e->getMessage());
        }
    }

    /**
     * Envoyer une notification SMS
     */
    public function envoyerSMS(User $user, string $message): void
    {
        if (!$user->telephone) {
            Log::warning("Pas de numéro de téléphone pour l'utilisateur {$user->id}");
            return;
        }

        try {
            // Intégration avec un service SMS (Twilio, Vonage, etc.)
            // Http::post('https://api.twilio.com/...', [...]);

            $this->creerNotification($user, 'SMS', $message, 'sms');

        } catch (\Exception $e) {
            Log::error("Erreur envoi SMS: " . $e->getMessage());
        }
    }

    /**
     * Créer une notification en base de données
     */
    public function creerNotification(
        User $user,
        string $titre,
        string $message,
        string $type = 'info',
        array $data = []
    ): Notification {
        return Notification::create([
            'user_id' => $user->id,
            'titre' => $titre,
            'message' => $message,
            'type' => $type,
            'data' => $data,
            'lu' => false,
        ]);
    }

    /**
     * Marquer une notification comme lue
     */
    public function marquerCommeLue(Notification $notification): void
    {
        $notification->update(['lu' => true]);
    }

    /**
     * Marquer toutes les notifications d'un utilisateur comme lues
     */
    public function marquerToutesCommeLues(User $user): void
    {
        Notification::where('user_id', $user->id)
            ->where('lu', false)
            ->update(['lu' => true]);
    }

    /**
     * Obtenir les notifications non lues d'un utilisateur
     */
    public function getNotificationsNonLues(User $user, ?int $limit = 20)
    {
        return Notification::where('user_id', $user->id)
            ->where('lu', false)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtenir toutes les notifications d'un utilisateur
     */
    public function getNotifications(User $user, ?int $limit = 50, bool $nonLuesSeulement = false)
    {
        $query = Notification::where('user_id', $user->id);

        if ($nonLuesSeulement) {
            $query->where('lu', false);
        }

        return $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Compter les notifications non lues
     */
    public function countNotificationsNonLues(User $user): int
    {
        return Notification::where('user_id', $user->id)
            ->where('lu', false)
            ->count();
    }

    /**
     * Supprimer une notification
     */
    public function supprimerNotification(Notification $notification): void
    {
        $notification->delete();
    }

    /**
     * Nettoyer les anciennes notifications
     */
    public function nettoyerAnciennesNotifications(int $jours = 30): void
    {
        Notification::where('created_at', '<', now()->subDays($jours))
            ->delete();
    }

    /**
     * Envoyer une notification de confirmation de commande
     */
    public function notifierConfirmationCommande(User $client, $commande): void
    {
        $this->envoyerPush(
            $client,
            'Commande confirmée',
            "Votre commande #{$commande->reference} a été confirmée.",
            ['type' => 'commande', 'commande_id' => $commande->id]
        );

        $this->envoyerEmail(
            $client,
            "Confirmation de commande #{$commande->reference}",
            'emails.confirmation_commande',
            ['commande' => $commande]
        );
    }

    /**
     * Envoyer une notification de statut de livraison
     */
    public function notifierStatutLivraison(User $client, $commande, string $nouveauStatut): void
    {
        $messages = [
            'en_livraison' => "Votre commande est en route !",
            'livree' => "Votre commande a été livrée. Bon appétit !",
            'annulee' => "Votre commande a été annulée.",
        ];

        $message = $messages[$nouveauStatut] ?? "Statut de votre commande: {$nouveauStatut}";

        $this->envoyerPush(
            $client,
            'Mise à jour de livraison',
            $message,
            ['type' => 'livraison', 'commande_id' => $commande->id, 'statut' => $nouveauStatut]
        );
    }

    /**
     * Envoyer une notification de promotion
     */
    public function notifierPromotion(User $client, $promotion): void
    {
        $this->envoyerPush(
            $client,
            'Nouvelle promotion !',
            $promotion->titre,
            ['type' => 'promotion', 'promotion_id' => $promotion->id]
        );
    }

    /**
     * Envoyer une notification de rappel de réservation
     */
    public function notifierRappelReservation(User $client, $reservation): void
    {
        $this->envoyerPush(
            $client,
            'Rappel de réservation',
            "N'oubliez pas votre réservation demain à {$reservation->date_heure->format('H:i')} chez {$reservation->restaurant->nom}.",
            ['type' => 'reservation', 'reservation_id' => $reservation->id]
        );

        $this->envoyerEmail(
            $client,
            'Rappel de réservation',
            'emails.rappel_reservation',
            ['reservation' => $reservation]
        );
    }

    /**
     * Diffuser une notification à plusieurs utilisateurs
     */
    public function diffuserNotification(array $users, string $titre, string $message, array $data = []): void
    {
        foreach ($users as $user) {
            $this->envoyerPush($user, $titre, $message, $data);
        }
    }

    /**
     * Envoyer une notification aux livreurs disponibles dans une zone
     */
    public function notifierLivreursZone(array $livreurs, string $titre, string $message, array $data = []): void
    {
        foreach ($livreurs as $livreur) {
            if ($livreur->statut === 'disponible') {
                $this->envoyerPush($livreur, $titre, $message, $data);
            }
        }
    }
}
