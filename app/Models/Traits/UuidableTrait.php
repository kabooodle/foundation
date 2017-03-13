<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

use Ramsey\Uuid\Uuid;

/**
 * Class UuidableTrait
 * @package Kabooodle\Models\Traits
 */
trait UuidableTrait
{
    public static function bootUuidableTrait()
    {
        self::saving(function ($model) {
            $model->uuid = Uuid::uuid4()->toString();
        });
    }
}
