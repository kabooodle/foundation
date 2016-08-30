<?php

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
        self::saving(function($model){
            $model->uuid = Uuid::uuid4()->toString();
        });
    }
}