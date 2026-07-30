<?php

namespace App\Models;

abstract class SoftDeleteModel extends BaseModel
{
    use SoftDeletes;

    const DELETED_AT = 'supprime_le';
}
