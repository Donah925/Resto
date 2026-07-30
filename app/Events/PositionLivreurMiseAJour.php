<?php

namespace App\Events;

use App\Models\Livraison;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PositionLivreurMiseAJour implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public string $livraisonId;
    public float $latitude;
    public float $longitude;

    /**
     * Create a new event instance.
     */
    public function __construct(
        string $livraisonId,
        float $latitude,
        float $longitude
    ) {
        $this->livraisonId = $livraisonId;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('livraison.' . $this->livraisonId),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'livreur.position';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'lat' => $this->latitude,
            'lng' => $this->longitude,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
