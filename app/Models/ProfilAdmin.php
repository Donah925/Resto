<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProfilAdmin extends BaseModel
{
    protected $table = 'profils_admin';

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

    public function restaurants(): BelongsToMany
    {
        return $this->belongsToMany(
            Restaurant::class,
            'restaurant_admin',
            'admin_id',
            'restaurant_id'
        );
    }
}
