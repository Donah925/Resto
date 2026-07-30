<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfilSuperAdmin extends BaseModel
{
    protected $table = 'profils_super_admin';

    protected $fillable = [
        'utilisateur_id',
        'permissions',
        'notes',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    // ===== RELATIONS =====

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }
}
