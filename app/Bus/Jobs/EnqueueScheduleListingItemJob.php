<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Jobs;

use Exception;
use Carbon\Carbon;
use Kabooodle\Models\Queues;
use Kabooodle\Models\ListingItems;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class EnqueueScheduleListingItemJob
 */
class EnqueueScheduleListingItemJob extends AbstractEnqueueJob implements ShouldQueue
{
    /**
     * @var int
     */
    public $queuesId;

    /**
     * @var ListingItems
     */
    public $listingItem;

    /**
     * @param ListingItems $listingItem
     */
    public function __construct(ListingItems $listingItem)
    {
        $this->listingItem = $listingItem;
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

    public function handle()
    {
        /** @var ListingItems $listingItem */
        $listingItem = $this->listingItem;

        // Update the listing item status as "processing"
        $this->updateQueueStatus($this->queuesId, Carbon::now(), Queues::STATUS_PROCESSING, $this->job->attempts());

        // Post to FACEBOOK.
        // HERE
        try {

        } catch (Exception $e) {}

        // Update the status to the appropriate status based on the result.
        $this->updateListingItemsStatus([$listingItem->id], Carbon::now(), ListingItems::STATUS_SUCCESS);

        // Update the associated queue in the DB
        $this->updateQueueStatus($this->queuesId, Carbon::now(), Queues::STATUS_SUCCESS, $this->job->attempts());

        $this->job->delete();

        return;
    }
}
