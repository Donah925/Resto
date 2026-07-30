<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class BaseModel extends Model
{
    use HasUuids;

    // UUID comme clé primaire
    public $incrementing = false;
    protected $keyType = 'string';

    // Mapping des timestamps français
    const CREATED_AT = 'cree_le';
    const UPDATED_AT = 'modifie_le';

    protected $dateFormat = 'Y-m-d H:i:s';

    // Dates par défaut
    protected $casts = [
        'cree_le' => 'datetime',
        'modifie_le' => 'datetime',
    ];

    /**
     * Génère automatiquement un UUID si non fourni
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
}
