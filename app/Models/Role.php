<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    // On hérite de Spatie mais on adapte les timestamps
    const CREATED_AT = 'cree_le';
    const UPDATED_AT = 'modifie_le';
}
