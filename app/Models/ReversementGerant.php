<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReversementGerant extends BaseModel
{
    protected $table = 'reversements_gerants';

    protected $fillable = [
        'restaurant_id',
        'gerant_id',
        'periode_debut',
        'periode_fin',
        'montant_brut',
        'commission_plateforme',
        'montant_net',
        'statut',
        'date_paiement',
        'reference_paiement',
    ];

    protected $casts = [
        'periode_debut' => 'date',
        'periode_fin' => 'date',
        'montant_brut' => 'decimal:2',
        'commission_plateforme' => 'decimal:2',
        'montant_net' => 'decimal:2',
        'date_paiement' => 'date',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function gerant(): BelongsTo
    {
        return $this->belongsTo(ProfilGerant::class, 'gerant_id');
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }
}
