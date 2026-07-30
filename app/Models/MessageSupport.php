<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageSupport extends BaseModel
{
    protected $table = 'messages_support';

    protected $fillable = [
        'ticket_id',
        'utilisateur_id',
        'message',
        'est_interne',
        'date_envoi',
    ];

    protected $casts = [
        'date_envoi' => 'datetime',
        'est_interne' => 'boolean',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(TicketSupport::class, 'ticket_id');
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class);
    }
}
