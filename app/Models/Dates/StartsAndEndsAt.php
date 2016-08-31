<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models\Dates;

use Carbon\Carbon;

/**
 * Class StartsAndEndsAt
 * @package Kabooodle\Models\Dates
 */
class StartsAndEndsAt
{
    /**
     * @var Carbon
     */
    private $startsAt;

    /**
     * @var Carbon
     */
    private $endsAt;

    /**
     * StartsAndEndsAt constructor.
     *
     * @param $startsAtTimestamp
     * @param $endsAtTimestamp
     */
    public function __construct($startsAtTimestamp, $endsAtTimestamp)
    {
        $this->startsAt = Carbon::createFromTimestamp($startsAtTimestamp);
        $this->endsAt = Carbon::createFromTimestamp($endsAtTimestamp);
    }

    /**
     * @return Carbon
     */
    public function getStartsAt()
    {
        return $this->startsAt;
    }

    /**
     * @return Carbon
     */
    public function getEndsAt()
    {
        return $this->endsAt;
    }
}