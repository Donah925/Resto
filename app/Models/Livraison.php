<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Livraison extends BaseModel
{
    protected $table = 'livraisons';

    protected $fillable = [
        'commande_id',
        'livreur_id',
        'statut',
        'latitude_depart',
        'longitude_depart',
        'latitude_arrivee',
        'longitude_arrivee',
        'distance_km',
        'duree_estimee',
        'duree_reelle',
        'note',
        'commentaire',
        'prise_en_charge_le',
        'livree_le',
    ];

    protected $casts = [
        'latitude_depart' => 'decimal:8',
        'longitude_depart' => 'decimal:8',
        'latitude_arrivee' => 'decimal:8',
        'longitude_arrivee' => 'decimal:8',
        'distance_km' => 'decimal:2',
        'duree_estimee' => 'integer',
        'duree_reelle' => 'integer',
        'note' => 'integer',
        'prise_en_charge_le' => 'datetime',
        'livree_le' => 'datetime',
    ];

    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class, 'commande_id');
    }

    public function livreur(): BelongsTo
    {
        return $this->belongsTo(ProfilLivreur::class, 'livreur_id');
    }
}
