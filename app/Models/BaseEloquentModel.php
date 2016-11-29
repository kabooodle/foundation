<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use Eloquent;
use Carbon\Carbon;
use ReflectionClass;
use Illuminate\Support\Str;

/**
 * Class BaseEloquentModel
 * @package Kabooodle\Models
 */
class BaseEloquentModel extends Eloquent
{
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
            if ($model->created_by) {
                $model->created_by = user()->id;
            }
        });

        self::updating(function ($model) {
            if ($model->updated_by) {
                $model->updated_by = user()->id;
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
     * @param $v
     *
     * @return string
     */
    public function getCreatedAtAttribute($v)
    {
        return Carbon::createFromFormat(DATE_ISO8601, $this->convertDateTimeTo8601($v));
    }

    /**
     * @param $v
     *
     * @return string
     */
    public function getUpdatedAtAttribute($v)
    {
        return Carbon::createFromFormat(DATE_ISO8601, $this->convertDateTimeTo8601($v));
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
     * @param string $format
     *
     * @return null
     */
    public function createdAtHuman($format = 'm-d-Y h:ia')
    {
        if ($this->created_at) {
            return $this->created_at->format($format);
        }

        return null;
    }

    /**
     * @return null
     */
    public function createdAtHumanNoTime()
    {
        return $this->createdAtHuman('m-d-Y');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function humanize($value)
    {
        return humanizeDateTime($value);
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function humanizeNoTime($value)
    {
        return humanizeDate($value);
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
     * @return null|string
     */
    public function getCreatedAtHumanAttribute()
    {
        return $this->createdAtHuman();
    }

    /**
     * @return null|string
     */
    public function getUpdatedAtHumanAttribute()
    {
        return $this->updatedAtHuman();
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

        return array_filter($constants, function($k) use ($startsWith) {
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
