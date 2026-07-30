<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CodePromo extends BaseModel
{
    protected $table = 'codes_promo';

    protected $fillable = [
        'code',
        'description',
        'type_reduction',
        'valeur_reduction',
        'nombre_utilisations_max',
        'nombre_utilisations',
        'date_debut',
        'date_fin',
        'statut',
        'campagne_id',
        'restaurant_id',
    ];

    protected $casts = [
        'valeur_reduction' => 'decimal:2',
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
    ];

    public function campagne(): BelongsTo
    {
        return $this->belongsTo(CampagneMarketing::class, 'campagne_id');
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function utilisations(): HasMany
    {
        return $this->hasMany(UtilisationCodePromo::class, 'code_promo_id');
    }
}
