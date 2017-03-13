<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Sofa\Revisionable\Revisionable;
use Sofa\Revisionable\Laravel\RevisionableTrait;

/**
 * Class Notifications
 * @package Kabooodle\Models
 */
class Notifications extends BaseEloquentModel implements Revisionable
{
    use RevisionableTrait;

    /**
     * @var string
     */
    protected $table = 'notifications';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'active',
        'group',
        'required_subscription_type',
        'type_sms',
        'type_email',
        'type_web'
    ];
}
