<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends BaseModel
{
    protected $table = 'notifications';

    protected $fillable = [
        'notifiable_type',
        'notifiable_id',
        'type',
        'titre',
        'message',
        'donnees',
        'lu_a',
        'lu_par',
    ];

    protected $casts = [
        'donnees' => 'array',
        'lu_a' => 'datetime',
    ];

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function lecteur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'lu_par');
    }

    public function lectures(): HasMany
    {
        return $this->hasMany(NotificationLecture::class, 'notification_id');
    }
}
