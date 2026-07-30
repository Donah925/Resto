<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampagneMarketing extends BaseModel
{
    protected $table = 'campagnes_marketing';

    protected $fillable = [
        'nom',
        'description',
        'type',
        'date_debut',
        'date_fin',
        'budget',
        'statut',
        'cible',
        'restaurant_id',
        'created_by',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'budget' => 'decimal:2',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'created_by');
    }

    public function codesPromo(): HasMany
    {
        return $this->hasMany(CodePromo::class, 'campagne_id');
    }

    public function resultats(): HasMany
    {
        return $this->hasMany(ResultatCampagne::class, 'campagne_id');
    }
}
