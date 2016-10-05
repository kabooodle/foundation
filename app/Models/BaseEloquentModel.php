<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use Carbon\Carbon;
use Eloquent;

/**
 * Class BaseEloquentModel
 * @package Kabooodle\Models
 */
class BaseEloquentModel extends Eloquent
{
    /**
     * @var array
     */
    public static $revisionableEvents = ['Updated', 'Deleted', 'Restored'];

    /**
     * @var bool
     */
    public static $perEnvironment = true;

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
     * @param $v
     *
     * @return string
     */
    public function getCreatedAtAttribute($v)
    {
        return Carbon::createFromFormat(DATE_ISO8601,$this->convertDateTimeTo8601($v));
    }

    /**
     * @param $v
     *
     * @return string
     */
    public function getUpdatedAtAttribute($v)
    {
        return Carbon::createFromFormat(DATE_ISO8601,$this->convertDateTimeTo8601($v));
    }

    /**
     * @param $date
     *
     * @return string
     */
    public function convertDateTimeTo8601($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format(DATE_ISO8601);
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeNoAppends($query)
    {
        return $query->setAppends([]);
    }

    /**
     * @return null|string
     */
    public function createdAtHuman()
    {
        if ($this->created_at) {
            return $this->created_at->format('m-d-Y h:ia');
        }

        return null;
    }

    /**
     * @return null|string
     */
    public function updatedAtHuman()
    {
        if ($this->updated_at) {
            return $this->updated_at->format('m-d-Y h:ia');
        }

        return null;
    }

    /**
     * @return mixed
     */
    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    /**
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        // Magic method for calling, what are already magic properties on the model, as a function
        // For example, "username" property would be called : $model->username however we can now invoke
        // this retrieval using a method call: $model->getUsername()
        if (starts_with($method, 'get')) {
            $methodAsParameter = snake_case(str_replace('get', '', $method));
            if (parent::__get($methodAsParameter)) {
                return parent::__get($methodAsParameter);
            }
        }

        return parent::__call($method, $parameters);
    }
}