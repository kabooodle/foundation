<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Sofa\Revisionable\Revisionable;
use Kabooodle\Models\Traits\UuidableTrait;
use Kabooodle\Presenters\PresentableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kabooodle\Models\Traits\EmojifyableTrait;
use Sofa\Revisionable\Laravel\RevisionableTrait;
use Kabooodle\Libraries\Linkify\LinkifyableTrait;
use Kabooodle\Presenters\Models\Comments\CommentsModelPresenter;

/**
 * Class Comments
 * @package Kabooodle\Models
 */
class Comments extends BaseEloquentModel implements Revisionable
{
    use EmojifyableTrait, LinkifyableTrait, PresentableTrait, RevisionableTrait, SoftDeletes, UuidableTrait;

    const CONVERT_EMOJI = true;

    /**
     * @var string
     */
    protected $table = 'comments';

    /**
     * @var string
     */
    protected $presenter = CommentsModelPresenter::class;

    /**
     * @var array
     */
    protected $with = [
        'author'
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'user_id' => 'int',
        'uuid' => 'string',
        'commentable_parent_id' => 'int',
        'commentable_id' => 'int',
        'commentable_type' => 'string',
        'text_raw' => 'string',
        'text' => 'string',
        'reference_url' => 'string'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'commentable_parent_id',
        'commentable_id',
        'commentable_type',
        'text_raw',
        'reference_url'
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'commentable_id',
        'commentable_type'
    ];

    public static function boot()
    {
        parent::boot();

        // Dont allow the ability to set "text" on the entity.
        self::saving(function (self $model) {
            $model->text = $model->text_raw;
        });
    }

    public static function getRules()
    {
        return [
            'text_raw' => 'required'
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->author();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function commentable()
    {
        return $this->morphTo('commentable');
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getTextAttribute($value)
    {
        $text = $this->linkify($value);
        if (self::CONVERT_EMOJI) {
            return $this->emojify($text);
        }

        return $text;
    }
}
