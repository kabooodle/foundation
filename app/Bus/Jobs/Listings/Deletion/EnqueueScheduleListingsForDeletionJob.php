<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Jobs\Listings\Deletion;

use Carbon\Carbon;
use Kabooodle\Models\Queues;
use Kabooodle\Models\Listings;
use Kabooodle\Models\ListingItems;
use Kabooodle\Libraries\QueueHelper;
use Kabooodle\Bus\Jobs\AbstractEnqueueJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Kabooodle\Bus\Events\Listings\ListingItemWasQueued;

/**
 * Class EnqueueScheduleListingsForDeletionJob
 */
class EnqueueScheduleListingsForDeletionJob extends AbstractEnqueueJob implements ShouldQueue
{
    /**
     * @var array
     */
    public $listingIds;

    /**
     * @var int
     */
    public $queuesId;

    /**
     * @var
     */
    public $timestamp;

    /**
     * @param array $listingIds
     */
    public function __construct(array $listingIds)
    {
        $this->listingIds = $listingIds;
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
     * Step 1 is FacebookDeletionEnqueuerCommand
     * Step 2 is EnqueueScheduleListingsForDeletionJob
     * Step 3 is EnqueueScheduleListingItemForDeletionJob
     */
    public function handle()
    {
        if (! $this->listingIds || count($this->listingIds) == 0){
            return;
        }

        $this->timestamp = Carbon::now();

        // Update the Queues status to processing.
        $this->updateQueueStatus($this->queuesId, $this->timestamp, Queues::STATUS_PROCESSING, $this->job->attempts());

        // Collection that will contain all the listings' listing items, ignoring their origin.
        $listingItems = collect([]);

        /** @var Collection $listingModels */
        $listingModels = Listings::whereIn('id', $this->listingIds)->with(['listingItems' => function($q){
            $q->whereNotIn('status', [ListingItems::STATUS_DELETED, ListingItems::STATUS_SCHEDULED_DELETE, ListingItems::STATUS_QUEUED_DELETE]);
        }])->get();

        // We shuffle just to randomize the parent listings and keep our process as random as possible.
        $shuffledListings = $listingModels->shuffle();

        // We want to extract all the listing items from the parent listings so we can queue them all individually.
        foreach($shuffledListings as $listing) {

            // Get the id's because we want as nominal a payload as possible
            $listingItemIds = $listing->listingItems->shuffle()->pluck('id')->toArray();

            foreach ($listingItemIds as $itemId) {

                // Push the item into the collection of items.
                $listingItems->push($itemId);
                // Build the job
                $job = $this->buildJob($itemId);

                // Add the listing item to the queue
                $this->dispatch($job);
//
//                event(new ListingItemWasQueued($job));

                unset($job);
            }
        }

        $this->updateListingItemsStatus($listingItems->shuffle()->pluck('id')->toArray(), $this->timestamp, ListingItems::STATUS_QUEUED_DELETE);

        $this->job->delete();

        // Get all the ids of the listings
        $listingsIds = $shuffledListings->pluck('id')->toArray();

        // Update status again of the listings, this time as "processing".
        $this->updateListingsStatus($listingsIds,  $this->timestamp, Listings::STATUS_PROCESSING_DELETE);

        return;
    }

    /**
     * @param int $itemId
     *
     * @return EnqueueScheduleListingItemForDeletionJob
     */
    public function buildJob(int $itemId)
    {
        $queueConnectionName = QueueHelper::pickFacebookLister();
        // Create our job class.
        $job = new EnqueueScheduleListingItemForDeletionJob($itemId);
        $job->onConnection($queueConnectionName);

        // Store details about the job in the DB for our own personal records.
        $localQueueDb = $this->createQueueStatus($queueConnectionName, $queueConnectionName, Queues::STATUS_QUEUED, serialize($job));

        // Tell the job which queue id it is associated with.
        $job->setQueuesId($localQueueDb->id);


        return $job;
    }
}
