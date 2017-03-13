<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Listings;

use Kabooodle\Bus\Jobs\EnqueueScheduleListingItemJob;

/**
 * Class ListingItemWasQueued
 */
final class ListingItemWasQueued
{
    /**
     * @var EnqueueScheduleListingItemJob
     */
    public $job;

    /**
     * @param EnqueueScheduleListingItemJob $job
     */
    public function __construct(EnqueueScheduleListingItemJob $job)
    {
        $this->job = $job;
    }

    /**
     * @return EnqueueScheduleListingItemJob
     */
    public function getJob(): EnqueueScheduleListingItemJob
    {
        return $this->job;
    }
}
