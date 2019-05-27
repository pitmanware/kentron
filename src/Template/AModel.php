<?php

    namespace Kentron\Template;

    use Illuminate\Database\Eloquent\Model as IlluminateModel;

    abstract class AModel extends IlluminateModel
    {
        public $timestamps = false;

        const CREATED_AT = null;
        const UPDATED_AT = null;
        const DELETED_AT = null;
    }
