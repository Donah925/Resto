<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produit extends SoftDeleteModel
{
    use HasTranslations;

    protected $table = 'produits';

    public array $translatable = ['nom', 'description'];

    protected $fillable = [
        'categorie_id',
        'restaurant_id',
        'nom',
        'description',
        'prix',
        'prix_promo',
        'image',
        'images',
        'est_visible',
        'est_en_promo',
        'temps_preparation',
        'calories',
        'allergenes',
        'ingredients',
        'personnalisation_possible',
        'options_personnalisation',
    ];

    protected $casts = [
        'prix' => 'decimal:2',
        'prix_promo' => 'decimal:2',
        'images' => 'array',
        'est_visible' => 'boolean',
        'est_en_promo' => 'boolean',
        'temps_preparation' => 'integer',
        'calories' => 'integer',
        'allergenes' => 'array',
        'ingredients' => 'array',
        'personnalisation_possible' => 'boolean',
        'options_personnalisation' => 'array',
    ];

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function lignesCommande(): HasMany
    {
        return $this->hasMany(LigneCommande::class, 'produit_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(OptionProduit::class, 'produit_id');
    }

    public function favoris(): HasMany
    {
        return $this->hasMany(FavorisClient::class, 'produit_id');
    }

    public function scopeVisibles($query)
    {
        return $query->where('est_visible', true);
    }

    public function scopeEnPromo($query)
    {
        return $query->where('est_en_promo', true);
    }

    public function getPrixFinalAttribute(): float
    {
        return $this->est_en_promo && $this->prix_promo 
            ? (float) $this->prix_promo 
            : (float) $this->prix;
    }
}
