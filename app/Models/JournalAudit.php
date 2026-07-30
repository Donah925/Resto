<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalAudit extends BaseModel
{
    protected $table = 'journaux_audit';

    protected $fillable = [
        'utilisateur_id',
        'action',
        'model_type',
        'model_id',
        'ancien_etat',
        'nouvel_etat',
        'adresse_ip',
        'user_agent',
        'date_action',
    ];

    protected $casts = [
        'ancien_etat' => 'array',
        'nouvel_etat' => 'array',
        'date_action' => 'datetime',
    ];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class);
    }
}
