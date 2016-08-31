<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

/**
 * Class User
 * @package Kabooodle\Models
 */
class Permissions extends BaseEloquentModel
{
    protected $table = 'permissions';

    protected $fillable = [
        'slug',
        'name'

    ];
}
