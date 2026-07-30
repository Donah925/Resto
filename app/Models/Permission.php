<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    const CREATED_AT = 'cree_le';
    const UPDATED_AT = 'modifie_le';
}
