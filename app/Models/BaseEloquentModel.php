<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Eloquent;
use Kabooodle\Models\Contracts\Hashable;
use Kabooodle\Models\Traits\HashableTrait;
use ReflectionClass;
use Illuminate\Support\Str;
use Kabooodle\Models\Traits\EloquentDatesTrait;

/**
 * Class BaseEloquentModel
 * @package Kabooodle\Models
 */
class BaseEloquentModel extends Eloquent implements Hashable
{
    use EloquentDatesTrait;
    use HashableTrait;

    /**
     * @var bool
     */
    public static $perEnvironment = true;

    /**
     * @var array
     */
    public static $revisionableEvents = ['Updated', 'Deleted', 'Restored'];

    /**
     * @var array
     */
    protected $appends = [
        'created_at_human',
        'updated_at_human'
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if ($model->created_by && webUser()) {
                $model->created_by = webUser()->id;
            }
        });

        self::updating(function ($model) {
            if ($model->updated_by && webUser()) {
                $model->updated_by = webUser()->id;
            }
        });
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeNoEagerLoads($query)
    {
        return $query->setEagerLoads([]);
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeNoAppends($query)
    {
        $this->setAppends([]);

        return $query;
    }

    /**
     * @return mixed
     */
    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    /**
     * @return array
     */
    public static function getConstants()
    {
        $class = new ReflectionClass(get_called_class());

        return $class->getConstants();
    }

    /**
     * @param $startsWith
     * @return array
     */
    public static function getConstantsStartsWith($startsWith)
    {
        $constants = self::getConstants();

        return array_filter($constants, function ($k) use ($startsWith) {
            return Str::startsWith($k, $startsWith);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
//        // Magic method for calling, what are already magic properties on the model, as a function
//        // For example, "username" property would be called : $model->username however we can now invoke
//        // this retrieval using a method call: $model->getUsername()
//        if (starts_with($method, 'get')) {
//            $methodAsParameter = snake_case(str_replace('get', '', $method));
//            if (parent::__get($methodAsParameter)) {
//                return parent::__get($methodAsParameter);
//            }
//        }

        return parent::__call($method, $parameters);
    }
}
