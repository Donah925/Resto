<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Avis extends BaseModel
{
    protected $table = 'avis';

    protected $fillable = [
        'restaurant_id',
        'produit_id',
        'utilisateur_id',
        'note',
        'commentaire',
        'statut',
        'reponse_gerant',
        'date_reponse',
    ];

    protected $casts = [
        'note' => 'integer',
        'date_reponse' => 'datetime',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class);
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class);
    }

    public function reponses(): HasMany
    {
        return $this->hasMany(ReponseAvis::class, 'avis_id');
    }

    public function signalements(): HasMany
    {
        return $this->hasMany(SignalementAvis::class, 'avis_id');
    }
}
