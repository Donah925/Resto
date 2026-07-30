<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultatCampagne extends BaseModel
{
    protected $table = 'resultats_campagnes';

    protected $fillable = [
        'campagne_id',
        'date',
        'nombre_vues',
        'nombre_clics',
        'nombre_conversions',
        'chiffre_affaires_genere',
        'cout_publicitaire',
    ];

    protected $casts = [
        'date' => 'datetime',
        'chiffre_affaires_genere' => 'decimal:2',
        'cout_publicitaire' => 'decimal:2',
    ];

    public function campagne(): BelongsTo
    {
        return $this->belongsTo(CampagneMarketing::class, 'campagne_id');
    }
}
