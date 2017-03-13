<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Listings;

use Bugsnag;
use Exception;
use Kabooodle\Models\User;
use Illuminate\Bus\Queueable;
use Kabooodle\Models\ListingItems;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kabooodle\Bus\Jobs\EnqueueScheduleListingItemJob;
use Kabooodle\Bus\Events\Listings\ListingItemWasQueued;

/**
 * Class NotifyListingItemWasQueued
 */
class NotifyListingItemWasQueued implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param ListingItemWasQueued $event
     */
    public function handle(ListingItemWasQueued $event)
    {
        /** @var EnqueueScheduleListingItemJob $job */
        $job = $event->getJob();

        /** @var ListingItems $listingItem */
        $listingItem = $job->getListingItem();

        /** @var User $owner */
        $owner = $listingItem->owner;

        try {
            $this->toEmail();
            $this->toDatabase();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }
    }

    public function toEmail()
    {

    }

    public function toDatabase()
    {

    }
}
