<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignalementAvis extends BaseModel
{
    protected $table = 'signalements_avis';

    protected $fillable = [
        'avis_id',
        'utilisateur_id',
        'raison',
        'description',
        'statut',
        'date_traitement',
        'traite_par',
    ];

    protected $casts = [
        'date_traitement' => 'datetime',
    ];

    public function avis(): BelongsTo
    {
        return $this->belongsTo(Avis::class, 'avis_id');
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class);
    }

    public function traitant(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'traite_par');
    }
}
