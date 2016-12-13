<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Jobs;

use Carbon\Carbon;
use Kabooodle\Bus\Events\Listings\ListingItemWasQueued;
use Kabooodle\Models\Queues;
use Kabooodle\Models\Listings;
use Kabooodle\Models\ListingItems;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class EnqueueScheduleListingsJob
 */
class EnqueueScheduleListingsJob extends AbstractEnqueueJob implements ShouldQueue
{
    /**
     * @var Collection
     */
    public $listingModels;

    /**
     * @var int
     */
    public $queuesId;

    /**
     * @var
     */
    public $timestamp;

    /**
     * @param Collection $listingModels
     */
    public function __construct(Collection $listingModels)
    {
        $this->listingModels = $listingModels;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setQueuesId($id)
    {
        $this->queuesId = $id;

        return $this;
    }

    /**
     * This is step 2 of 3.
     *
     * Step 1 is FacebookEnqueuerCommand
     * Step 2 is EnqueueScheduleListingsJob
     * Step 3 is EnqueueScheduleListingItemJob
     */
    public function handle()
    {
        $this->timestamp = Carbon::now();

        // Update the Queues status to processing.
        $this->updateQueueStatus($this->queuesId, $this->timestamp, Queues::STATUS_PROCESSING, $this->job->attempts());

        // Collection that will contain all the listings' listing items, ignoring their origin.
        $listingItems = collect([]);

        /** @var Collection $listingModels */
        $listingModels = $this->listingModels;

        // We shuffle just to randomize the parent listings and keep our process as random as possible.
        $shuffledListings = $listingModels->shuffle();

        // We want to extract all the listing items from the parent listings so we can queue them all individually.
        foreach($shuffledListings as $listing) {
            foreach ($listing->listingItems as $item) {

                // It is possible that the current item' status has been deleted since retrieving it from the DB.
                if (in_array($item->status, [ListingItems::STATUS_SCHEDULED]) && ! $item->isIgnored()) {

                    // Push the item into the collection of items.
                    $listingItems->push($item);
                }
            }
        }

        // Shuffle all the listing items, similar to above, to keep everything as random as possible.
        $shuffledListingItems = $listingItems->shuffle();

        $this->updateListingItemsStatus($shuffledListingItems->pluck('id')->toArray(), $this->timestamp, ListingItems::STATUS_QUEUED_LIST);

        // Iterate over the listing items and push them to the queue.
        foreach($shuffledListingItems as $shuffledListingItem) {

            // Build the job
            $job = $this->buildJob($shuffledListingItem);

            // Add the listing item to the queue
            $this->dispatch($job);

            event(new ListingItemWasQueued($job));

            unset($job);
        }

        $this->job->delete();

        // Get all the ids of the listings
        $listingsIds = $shuffledListings->pluck('id')->toArray();

        // Update status again of the listings, this time as "processing".
        $this->updateListingsStatus($listingsIds,  $this->timestamp, Listings::STATUS_PROCESSING);

        return;
    }

    /**
     * @param ListingItems $item
     *
     * @return EnqueueScheduleListingItemJob
     */
    public function buildJob(ListingItems $item)
    {
        // Create our job class.
        $job = new EnqueueScheduleListingItemJob($item);

        // Store details about the job in the DB for our own personal records.
        $localQueueDb = $this->createQueueStatus('default', Queues::STATUS_QUEUED, serialize($job));

        // Tell the job which queue id it is associated with.
        $job->setQueuesId($localQueueDb->id);
        $job->onConnection('iron-facebook-lister');

        return $job;
    }
}
