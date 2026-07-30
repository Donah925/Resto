<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketSupport extends BaseModel
{
    protected $table = 'tickets_support';

    protected $fillable = [
        'utilisateur_id',
        'sujet',
        'description',
        'statut',
        'priorite',
        'categorie',
        'assigne_a',
        'date_cloture',
        'cloture_par',
    ];

    protected $casts = [
        'date_cloture' => 'datetime',
    ];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class);
    }

    public function assigne(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'assigne_a');
    }

    public function cloturant(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'cloture_par');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(MessageSupport::class, 'ticket_id');
    }

    public function piecesJointes(): HasMany
    {
        return $this->hasMany(PieceJointeSupport::class, 'ticket_id');
    }
}
