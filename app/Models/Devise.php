<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Devise extends BaseModel
{
    protected $table = 'devises';

    protected $fillable = [
        'code',
        'nom',
        'symbole',
        'taux_change',
        'devise_reference_id',
        'actif',
    ];

    protected $casts = [
        'taux_change' => 'decimal:6',
        'actif' => 'boolean',
    ];

    public function deviseReference(): BelongsTo
    {
        return $this->belongsTo(Devise::class, 'devise_reference_id');
    }

    public function tauxChangeHistoriques(): HasMany
    {
        return $this->hasMany(TauxChangeHistorique::class, 'devise_id');
    }
}
