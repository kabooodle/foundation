<?php

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
