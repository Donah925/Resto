<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TauxChangeHistorique extends BaseModel
{
    protected $table = 'taux_change_historiques';

    protected $fillable = [
        'devise_id',
        'taux_change',
        'date_application',
        'source',
    ];

    protected $casts = [
        'taux_change' => 'decimal:6',
        'date_application' => 'datetime',
    ];

    public function devise(): BelongsTo
    {
        return $this->belongsTo(Devise::class, 'devise_id');
    }
}
