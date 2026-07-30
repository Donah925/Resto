<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TauxTaxe extends BaseModel
{
    protected $table = 'taux_taxes';

    protected $fillable = [
        'nom',
        'taux',
        'type',
        'date_debut',
        'date_fin',
        'actif',
        'applicable_a',
        'restaurant_id',
    ];

    protected $casts = [
        'taux' => 'decimal:4',
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'actif' => 'boolean',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
