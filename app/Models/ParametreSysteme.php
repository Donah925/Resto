<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParametreSysteme extends BaseModel
{
    protected $table = 'parametres_systeme';

    protected $fillable = [
        'cle',
        'valeur',
        'type',
        'description',
        'categorie',
        'modifiable',
        'modifie_par',
    ];

    protected $casts = [
        'modifiable' => 'boolean',
    ];

    public function modificateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'modifie_par');
    }
}
