<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLecture extends BaseModel
{
    protected $table = 'notifications_lectures';

    protected $fillable = [
        'notification_id',
        'utilisateur_id',
        'lu_a',
    ];

    protected $casts = [
        'lu_a' => 'datetime',
    ];

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class);
    }
}
