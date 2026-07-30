<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupeOption extends SoftDeleteModel
{
    use HasTranslations;

    protected $table = 'groupes_options';

    public array $translatable = ['nom'];

    protected $fillable = [
        'restaurant_id',
        'nom',
        'type_selection',
        'min_options',
        'max_options',
        'est_obligatoire',
    ];

    protected $casts = [
        'min_options' => 'integer',
        'max_options' => 'integer',
        'est_obligatoire' => 'boolean',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(OptionProduit::class, 'groupe_options_id');
    }
}
