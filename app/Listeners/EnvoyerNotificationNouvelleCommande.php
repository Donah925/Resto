<?php

namespace App\Listeners;

use App\Events\NouvelleCommandeRecue;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class EnvoyerNotificationNouvelleCommande implements ShouldQueue
{
    use InteractsWithQueue;

    private NotificationService $notificationService;

    /**
     * Create the event listener.
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(NouvelleCommandeRecue $event): void
    {
        $commande = $event->commande;

        // 1. Notification In-App au gérant
        if ($commande->restaurant && $commande->restaurant->gerant) {
            $this->notificationService->creerNotification(
                $commande->restaurant->gerant->utilisateur_id,
                'nouvelle_commande',
                'Nouvelle commande',
                "Commande #{$commande->numero} reçue ({$commande->montant_total} FCFA)",
                'in_app'
            );
        }

        // 2. SMS au client pour confirmer la bonne réception
        if ($commande->client && $commande->client->utilisateur && $commande->client->utilisateur->telephone) {
            $this->notificationService->envoyerSms(
                $commande->client->utilisateur->telephone,
                "Votre commande #{$commande->numero} a été reçue par le restaurant et est en cours de traitement."
            );
        }
    }
}
