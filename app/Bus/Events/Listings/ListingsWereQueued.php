<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Listings;

use Kabooodle\Bus\Jobs\EnqueueScheduleListingsJob;

/**
 * Class ListingsWereQueued
 */
final class ListingsWereQueued
{
    /**
     * @var EnqueueScheduleListingsJob
     */
    public $job;

    /**
     * @param EnqueueScheduleListingsJob $job
     */
    public function __construct(EnqueueScheduleListingsJob $job)
    {
        $this->job = $job;
    }

    /**
     * @return EnqueueScheduleListingsJob
     */
    public function getJob(): EnqueueScheduleListingsJob
    {
        return $this->job;
    }
}
