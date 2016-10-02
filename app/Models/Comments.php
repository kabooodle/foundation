<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use Sofa\Revisionable\Revisionable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sofa\Revisionable\Laravel\RevisionableTrait;
use Kabooodle\Libraries\Linkify\LinkifyableTrait;

/**
 * Class Comments
 * @package Kabooodle\Models
 */
class Comments extends BaseEloquentModel implements Revisionable
{
    use LinkifyableTrait, RevisionableTrait, SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'comments';

    /**
     * @var array
     */
    protected $attributes = [
        'user_id' => 'int',
        'commentable_parent_id' => 'int',
        'commentable_id' => 'int',
        'commentable_type' => 'string',
        'text_raw' => 'string',
        'text' => 'string',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'commentable_parent_id',
        'commentable_id',
        'commentable_type',
        'text_raw'
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'user_id'
    ];

    public static function boot()
    {
        parent::boot();

        // Dont allow the ability to set "text" on the entity.
        // We overload it here which ultimately is decorated with linkify() (below)
        self::saving(function(self $model){
            $model->text = $model->text_raw;
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function commentable()
    {
        return $this->morphTo('commentable');
    }

    /**
     * @param string $value
     */
    public function setTextAttribute($value)
    {
        $this->attributes['text'] = $this->linkify($value);
    }
}
