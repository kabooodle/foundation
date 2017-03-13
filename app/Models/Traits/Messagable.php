<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

use Kabooodle\Models\Threads;
use Kabooodle\Models\ThreadMessages;
use Kabooodle\Models\ThreadParticipants;

/**
 * Class Messagable
 */
trait Messagable
{
    /**
     * Message relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(ThreadMessages::class);
    }

    /**
     * Participants relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participants()
    {
        return $this->hasMany(ThreadParticipants::class);
    }

    /**
     * Thread relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function threads()
    {
        return $this->belongsToMany(
            Threads::class,
            ThreadParticipants::class,
            'user_id',
            'thread_id'
        );
    }

    /**
     * Returns the new messages count for user.
     *
     * @return int
     */
    public function newThreadsCount()
    {
        return $this->threadsWithNewMessages()->count();
    }

    /**
     * Returns all threads with new messages.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function threadsWithNewMessages()
    {
        return $this->threads()
            ->where(function ($q) {
                $q->whereNull(ThreadParticipants::getTableName() . '.last_read');
                $q->orWhere(Threads::getTableName() . '.updated_at', '>', $this->getConnection()->raw($this->getConnection()->getTablePrefix() . Models::table('participants') . '.last_read'));
            })->get();
    }
}