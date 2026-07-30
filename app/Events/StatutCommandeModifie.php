<?php

namespace App\Events;

use App\Models\Commande;
use App\Enums\StatutCommande;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StatutCommandeModifie implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public Commande $commande;
    public StatutCommande $ancienStatut;
    public StatutCommande $nouveauStatut;

    /**
     * Create a new event instance.
     */
    public function __construct(
        Commande $commande,
        StatutCommande $ancienStatut,
        StatutCommande $nouveauStatut
    ) {
        $this->commande = $commande;
        $this->ancienStatut = $ancienStatut;
        $this->nouveauStatut = $nouveauStatut;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            // Le client suit sa commande
            new PrivateChannel('client.' . $this->commande->client_id),
            // Le restaurant voit aussi le changement
            new PrivateChannel('restaurant.' . $this->commande->restaurant_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'commande.statut.modifie';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'commande_id' => $this->commande->id,
            'numero' => $this->commande->numero,
            'ancien_statut' => $this->ancienStatut->value,
            'nouveau_statut' => $this->nouveauStatut->value,
            'nouveau_statut_label' => $this->nouveauStatut->label(),
            'couleur' => $this->nouveauStatut->color(),
        ];
    }
}
