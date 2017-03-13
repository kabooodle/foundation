<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Dates;

use Carbon\Carbon;
use Kabooodle\Services\DateFactory;

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
        $dateFactory = app()->make(DateFactory::class);
        $this->startsAt = $dateFactory->parse($startsAtTimestamp);
        $this->endsAt = $dateFactory->parse($endsAtTimestamp);
    }

    /**
     * @return Carbon
     */
    public function getStartsAt(): Carbon
    {
        return $this->startsAt;
    }

    /**
     * @return Carbon
     */
    public function getEndsAt(): Carbon
    {
        return $this->endsAt;
    }
}
