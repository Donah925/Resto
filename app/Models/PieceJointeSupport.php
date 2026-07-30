<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PieceJointeSupport extends BaseModel
{
    protected $table = 'pieces_jointes_support';

    protected $fillable = [
        'ticket_id',
        'message_id',
        'nom_fichier',
        'chemin_fichier',
        'type_mime',
        'taille',
        'upload_par',
    ];

    protected $casts = [
        'taille' => 'integer',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(TicketSupport::class, 'ticket_id');
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(MessageSupport::class, 'message_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'upload_par');
    }
}
