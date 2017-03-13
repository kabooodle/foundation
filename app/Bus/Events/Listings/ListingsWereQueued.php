<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Listings;

use Kabooodle\Bus\Jobs\EnqueueScheduleListingsJob;

/**
 * Class ListingsWereQueued
 */
final class ListingsWereQueued
{
    /**
     * @var
     */
    public $listings;

    /**
     * @var EnqueueScheduleListingsJob
     */
    public $job;

    /**
     * @param                            $listings
     * @param EnqueueScheduleListingsJob $job
     */
    public function __construct($listings, EnqueueScheduleListingsJob $job)
    {
        $this->listings = $listings;
        $this->job = $job;
    }

    /**
     * @return mixed
     */
    public function getListings()
    {
        return $this->listings;
    }

    /**
     * @return EnqueueScheduleListingsJob
     */
    public function getJob(): EnqueueScheduleListingsJob
    {
        return $this->job;
    }
}
