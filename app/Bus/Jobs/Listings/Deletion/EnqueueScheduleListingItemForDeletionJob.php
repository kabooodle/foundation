<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Jobs\Listings\Deletion;

use DB;
use Bugsnag;
use Exception;
use Carbon\Carbon;
use Kabooodle\Models\Queues;
use Kabooodle\Models\Listings;
use Kabooodle\Models\ListingItems;
use Kabooodle\Bus\Jobs\AbstractEnqueueJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Facebook\Exceptions\FacebookThrottleException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Kabooodle\Foundation\Exceptions\FacebookTokenInvalidException;
use Kabooodle\Bus\Commands\Listings\DeleteListingItemFromFacebookCommand;

/**
 * Class EnqueueScheduleListingItemForDeletionJob
 */
class EnqueueScheduleListingItemForDeletionJob extends AbstractEnqueueJob implements ShouldQueue
{
    use DispatchesJobs;

    /**
     * @var int
     */
    public $queuesId;

    /**
     * @var int
     */
    public $listingItemId;

    /**
     * @var string
     */
    public $timestamp;

    /**
     * @param int $listingItemId
     */
    public function __construct(int $listingItemId)
    {
        $this->listingItemId = $listingItemId;
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
     * This is step 1 of 3.
     *
     * Step 1 is FacebookEnqueuerCommand
     * Step 2 is EnqueueScheduleListingsJob
     * Step 3 is EnqueueScheduleListingItemJob
     *
     * @return bool
     *
     * @throws Exception
     * @throws FacebookThrottleException
     */
    public function handle()
    {
        try {
            // Cache a sequential timestamp.
            $this->timestamp = Carbon::now();

            $listingItem = $this->getListingItem();

            // Update the listing item status as "processing"
            $this->updateQueueStatus($this->queuesId, $this->timestamp, Queues::STATUS_PROCESSING, $this->job->attempts());

            // We have a separate job that handles the posting to facebook
            $job = new DeleteListingItemFromFacebookCommand($listingItem->owner_id, $listingItem->listing_id, $listingItem->id);
            $this->dispatchNow($job);

            $this->successfulJobHandler();

            $this->job->delete();
        } catch (FacebookTokenInvalidException $e) {
            Bugsnag::notifyException($e);
            $this->failedJobHandler($listingItem, ['status_history' => 'Invalid Facebook token']);
            $this->job->delete();
        } catch (ModelNotFoundException $e) {
            Bugsnag::notifyException($e);
            $this->job->delete();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            $this->failedJobHandler($listingItem);

            throw $e;
        }

        if (! is_null($listingItem->listing->scheduled_for_deletion)) {
            $remainingListingItems = $this->getRemainingListingItems($listingItem->listing_id);

            // TODO: I added <= 5 as a buffer because 100% isn't the best. Figure out algorithm to better identify accuracy
            if (! $remainingListingItems || count($remainingListingItems) == 0 || count($remainingListingItems) <= 5) {
                // Update the status to the appropriate status based on the result.
                $this->updateListingsStatus([$listingItem->listing_id], $this->timestamp, Listings::STATUS_DELETED);
            }
        }

        return true;
    }

    /**
     * @param       $listingItem
     * @param array $attributes
     */
    public function failedJobHandler($listingItem, array $attributes = [])
    {
        // Update the status to the appropriate status based on the result.
        $this->updateListingItemsStatus([$listingItem->id], $this->timestamp, ListingItems::STATUS_FAILED, $attributes);

        // Update the associated queue in the DB
        $this->updateQueueStatus($this->queuesId, $this->timestamp, Queues::STATUS_FAILED, $this->job->attempts());
    }

    public function successfulJobHandler()
    {
        // Update the associated queue in the DB
        $this->updateQueueStatus($this->queuesId, $this->timestamp, Queues::STATUS_SUCCESS, $this->job->attempts());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function getListingItem()
    {
        return ListingItems::findOrFail($this->listingItemId);
    }

    /**
     * @param $listingId
     *
     * @return array
     */
    public function getRemainingListingItems($listingId)
    {
        $sql = 'select * FROM listing_items WHERE id not in (
                  select id from listing_items where status in ("success", "partial", "completed" "ignored_duplicate","failed", "throttled", "queued_delete", "deleted") and listing_id = ?
                ) and  listing_id = ?';

        return DB::select($sql, [$listingId, $listingId]);
    }
}
