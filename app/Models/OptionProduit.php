<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OptionProduit extends SoftDeleteModel
{
    use HasTranslations;

    protected $table = 'options_produit';

    public array $translatable = ['nom'];

    protected $fillable = [
        'produit_id',
        'groupe_options_id',
        'nom',
        'prix_supplementaire',
        'est_obligatoire',
        'quantite_max',
        'ordre_tri',
    ];

    protected $casts = [
        'prix_supplementaire' => 'decimal:2',
        'est_obligatoire' => 'boolean',
        'quantite_max' => 'integer',
        'ordre_tri' => 'integer',
    ];

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    public function groupeOptions(): BelongsTo
    {
        return $this->belongsTo(GroupeOption::class, 'groupe_options_id');
    }

    public function lignesCommande(): HasMany
    {
        return $this->hasMany(LigneCommande::class, 'option_id');
    }
}
