<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Participant;
use Kabooodle\Models\Traits\EloquentDatesTrait;

/**
 * Class ThreadParticipants
 */
class ThreadParticipants extends Participant
{
    use EloquentDatesTrait;

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'last_read'
    ];

    /**
     * @param $v
     *
     * @return mixed
     */
    public function getLastReadAttribute($v)
    {
        if ($v) {
            $time = Carbon::createFromFormat(DATE_ISO8601, $this->convertDateTimeTo8601($v));

            return user() ? $time->tz(user()->timezone) : $time;
        }

        return $v;
    }
}
