<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use Cmgmyr\Messenger\Models\Thread;
use Kabooodle\Models\Traits\EloquentDatesTrait;

/**
 * Class Threads
 */
class Threads extends Thread
{
    use EloquentDatesTrait;

    protected $appends = [
        'participants_names_excluding_creator'
    ];

    /**
     * @return mixed
     */
    public function getParticipantsNamesExcludingCreatorAttribute()
    {
        $participants = $this->participantsExcludingCreator;

        return implode(',', $participants->pluck('user.full_name')->toArray());
    }

    /**
     * @return mixed
     */
    public function participantsExcludingCreator()
    {
        return $this->participants()->where('user_id', '<>', $this->creator()->id);
    }
}