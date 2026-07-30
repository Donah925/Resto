<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug()
    {
        static::creating(function ($model) {
            if (empty($model->slug) && !empty($model->nom)) {
                $nom = is_array($model->nom) ? ($model->nom['fr'] ?? reset($model->nom)) : $model->nom;
                $model->slug = Str::slug($nom);
            }
        });
    }
}
