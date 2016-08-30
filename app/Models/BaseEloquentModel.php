<?php

namespace Kabooodle\Models;

use Eloquent;

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
        return $query->setAppends([]);
    }

    /**
     * @return null|string
     */
    public function createdAtHuman()
    {
        if ($this->created_at) {
            return $this->created_at->format('m-d-Y H:i:sa');
        }

        return null;
    }

    /**
     * @return null|string
     */
    public function updatedAtHuman()
    {
        if ($this->updated_at) {
            return $this->updated_at->format('m-d-Y H:ia');
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
}