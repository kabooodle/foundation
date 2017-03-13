<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Kabooodle\Models\Traits\EmojifyableTrait;
use Kabooodle\Libraries\Linkify\LinkifyableTrait;

/**
 * Class ThreadParticipants
 */
class ThreadMessages extends BaseEloquentModel
{
    use EmojifyableTrait, LinkifyableTrait;

    const CONVERT_EMOJI = true;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'messenger_messages';

    /**
     * The attributes that can be set with Mass Assignment.
     *
     * @var array
     */
    protected $fillable = [
        'thread_id',
        'user_id',
        'body'
    ];

    /**
     * Validation rules.
     *
     * @var array
     */
    protected $rules = [
        'body' => 'required',
    ];


    /**
     * @param string $value
     */
    public function setBodyAttribute($value)
    {
        $text = $this->linkify($value);
        $this->attributes['body'] = $text;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getBodyAttribute($value)
    {
        if (self::CONVERT_EMOJI) {
            $value = $this->emojify($value);
        }

        return $value;
    }

    /**
     * Thread relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo(Threads::class, 'thread_id', 'id');
    }

    /**
     * User relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Participants relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participants()
    {
        return $this->hasMany(ThreadParticipants::class, 'thread_id', 'thread_id');
    }

    /**
     * Recipients of this message.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recipients()
    {
        return $this->participants()->where('user_id', '!=', $this->user_id);
    }
}
