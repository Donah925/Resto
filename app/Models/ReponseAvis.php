<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReponseAvis extends BaseModel
{
    protected $table = 'reponses_avis';

    protected $fillable = [
        'avis_id',
        'utilisateur_id',
        'commentaire',
        'date_reponse',
    ];

    protected $casts = [
        'date_reponse' => 'datetime',
    ];

    public function avis(): BelongsTo
    {
        return $this->belongsTo(Avis::class, 'avis_id');
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class);
    }
}
