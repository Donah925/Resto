<?php

namespace App\Events;

use App\Models\Commande;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NouvelleCommandeRecue implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Commande $commande;

    /**
     * Create a new event instance.
     */
    public function __construct(Commande $commande)
    {
        $this->commande = $commande;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('restaurant.' . $this->commande->restaurant_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'commande.recue';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'commande_id' => $this->commande->id,
            'numero' => $this->commande->numero,
            'montant_total' => $this->commande->montant_total,
            'type_commande' => $this->commande->type_commande->value,
            'cree_le' => $this->commande->cree_le?->format('H:i:s'),
        ];
    }
}
