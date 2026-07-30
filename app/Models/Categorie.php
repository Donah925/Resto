<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends SoftDeleteModel
{
    use HasTranslations;

    protected $table = 'categories';

    public array $translatable = ['nom', 'description'];

    protected $fillable = [
        'restaurant_id',
        'parent_id',
        'nom',
        'description',
        'icone',
        'image',
        'couleur',
        'ordre_tri',
        'est_active',
    ];

    protected $casts = [
        'ordre_tri' => 'integer',
        'est_active' => 'boolean',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function enfants(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function produits(): HasMany
    {
        return $this->hasMany(Produit::class, 'categorie_id');
    }

    public function scopeActives($query)
    {
        return $query->where('est_active', true);
    }

    public function scopeOrdre($query)
    {
        return $query->orderBy('ordre_tri');
    }
}
