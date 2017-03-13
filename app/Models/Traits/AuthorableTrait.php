<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

use Kabooodle\Models\User;

/**
 * Class AuthorableTrait
 * @package Kabooodle\Models\Traits
 */
trait AuthorableTrait
{
    public static function bootAuthorableTrait()
    {
        self::updating(function ($model) {
            $model->updated_by = user()->id;
        });

        self::creating(function ($model) {
            $model->updated_by = user()->id;
            $model->created_by = user()->id;
        });

        self::deleting(function ($model) {
            $model->deleted_by = user()->id;
        });
    }

    /**
     * @return mixed
     */
    protected function getUserColumn()
    {
        return $this->userColumn;
    }

    /**
     * @return mixed
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, $this->getUserColumn());
    }

    /**
     * @return mixed
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, $this->getUserColumn());
    }

    /**
     * @return mixed
     */
    public function deletedBy()
    {
        return $this->belongsTo(User::class, $this->getUserColumn());
    }
}
