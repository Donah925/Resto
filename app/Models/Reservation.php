<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends BaseModel
{
    protected $table = 'reservations';

    protected $fillable = [
        'restaurant_id',
        'client_id',
        'table_id',
        'salle_id',
        'nombre_personnes',
        'date_reservation',
        'heure_arrivee',
        'heure_prevue_fin',
        'statut',
        'notes_client',
        'notes_restaurant',
        'confirme_le',
        'annule_le',
    ];

    protected $casts = [
        'nombre_personnes' => 'integer',
        'date_reservation' => 'date',
        'heure_arrivee' => 'datetime',
        'heure_prevue_fin' => 'datetime',
        'confirme_le' => 'datetime',
        'annule_le' => 'datetime',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(ProfilClient::class, 'client_id');
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(TableRestaurant::class, 'table_id');
    }

    public function salle(): BelongsTo
    {
        return $this->belongsTo(SalleRestaurant::class, 'salle_id');
    }
}
