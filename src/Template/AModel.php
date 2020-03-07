<?php

namespace Kentron\Template;

use Illuminate\Database\Eloquent\Model;

abstract class AModel extends Model
{
    public $timestamps = false;

    const CREATED_AT = null;
    const UPDATED_AT = null;
    const DELETED_AT = null;
}
