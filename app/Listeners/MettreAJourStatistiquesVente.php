<?php

namespace App\Listeners;

use App\Events\StatutCommandeModifie;
use App\Enums\StatutCommande;
use App\Models\JournalAudit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;

class MettreAJourStatistiquesVente implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(StatutCommandeModifie $event): void
    {
        // Si la commande passe à "Terminée", on peut déclencher des calculs de stats
        if ($event->nouveauStatut === StatutCommande::TERMINEE) {
            // Exemple : Mettre à jour un cache de chiffre d'affaires du jour
            $cleCache = 'ca_du_jour_restaurant_' . $event->commande->restaurant_id;
            
            // On utilise Redis/Cache pour incrémenter de manière atomique
            Cache::increment($cleCache, $event->commande->montant_total);

            // Journal d'audit
            JournalAudit::logger(
                'commande_terminee',
                'Commande',
                $event->commande->id,
                ['statut' => $event->ancienStatut->value],
                ['statut' => $event->nouveauStatut->value]
            );
        }
    }
}
