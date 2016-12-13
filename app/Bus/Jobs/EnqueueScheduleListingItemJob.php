<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Jobs;

use DB;
use Exception;
use Carbon\Carbon;
use Kabooodle\Models\Files;
use Kabooodle\Models\Listings;
use Kabooodle\Models\Queues;
use Kabooodle\Models\ListingItems;
use Illuminate\Contracts\Queue\ShouldQueue;
use Facebook\Exceptions\FacebookThrottleException;
use Kabooodle\Bus\Events\Listings\ListingItemWasListed;
use Kabooodle\Services\Social\Facebook\FacebookSdkService;
use Kabooodle\Services\Social\Facebook\Entities\PhotoDescription;
use Kabooodle\Foundation\Exceptions\Listings\ListingPhotoMissingException;

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
     * @var string
     */
    public $timestamp;

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

    /**
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

            $facebook = $this->getFacebookService();

            $listingItem = $this->getListingItem();

            // Update the listing item status as "processing"
            $this->updateQueueStatus($this->queuesId, $this->timestamp, Queues::STATUS_PROCESSING, $this->job->attempts());

            $facebookParams = $this->buildFacebookAlbumParams($listingItem);

            $facebook->postPhotoToGroupAlbum(
                $listingItem->fb_album_node_id,
                $facebookParams,
                $listingItem->owner->getFacebookUserToken()
            );

            event(new ListingItemWasListed);

            $this->job->delete();

            $this->successfulJobHandler($listingItem);

        } catch (Exception $e) {
            $this->failedJobHandler($listingItem);

            throw $e;
        }

        $remainingListingItems = $this->getRemainingListingItems($listingItem->listing_id);

        if (! $remainingListingItems || count($remainingListingItems) == 0) {
            // Update the status to the appropriate status based on the result.
            $this->updateListingsStatus([$listingItem->listing_id], $this->timestamp, Listings::STATUS_COMPLETED);
        }

        return true;
    }

    /**
     * @param $listingItem
     */
    public function failedJobHandler($listingItem)
    {
        // Update the status to the appropriate status based on the result.
        $this->updateListingItemsStatus([$listingItem->id], $this->timestamp, ListingItems::STATUS_SUCCESS);

        // Update the associated queue in the DB
        $this->updateQueueStatus($this->queuesId, $this->timestamp, Queues::STATUS_SUCCESS, $this->job->attempts());
    }

    /**
     * @param $listingItem
     */
    public function successfulJobHandler($listingItem)
    {
        // Update the status to the appropriate status based on the result.
        $this->updateListingItemsStatus([$listingItem->id], $this->timestamp, ListingItems::STATUS_SUCCESS);

        // Update the associated queue in the DB
        $this->updateQueueStatus($this->queuesId, $this->timestamp, Queues::STATUS_SUCCESS, $this->job->attempts());
    }

    /**
     * @param ListingItems $listingItem
     *
     * @return array
     * @throws ListingPhotoMissingException
     */
    public function buildFacebookAlbumParams(ListingItems $listingItem)
    {
        $photoDescr = new PhotoDescription($listingItem);

        $photoMessage = $listingItem->includeLinkInDescr() ? $photoDescr->getClaimUrl() : null;

        $image = $this->getListingImage($listingItem);

        if (!$image) {
            throw new ListingPhotoMissingException($listingItem->id);
        }

        return [
            'url' => $image->getURL(),
            'message' => $photoMessage
        ];
    }

    /**
     * @return ListingItems
     */
    public function getListingItem()
    {
        return $this->listingItem;
    }

    /**
     * @return \Illuminate\Foundation\Application|FacebookSdkService|mixed
     */
    public function getFacebookService()
    {
        return app(\Kabooodle\Services\Social\Facebook\FacebookSdkService::class);
    }

    /**
     * @param ListingItems $listingItem
     *
     * @return Files
     */
    public function getListingImage(ListingItems $listingItem)
    {
        return $listingItem->inventoryItem->firstImage();
    }

    /**
     * @param $listingId
     *
     * @return array
     */
    public function getRemainingListingItems($listingId)
    {
        $sql = 'select * FROM listing_items WHERE id not in (
                  select id from listing_items where status in ("success", "partial", "completed" "ignored_duplicate", "queued_delete", "deleted") and listing_id = ?
                ) and  listing_id = ?';

        return DB::select($sql, [$listingId, $listingId]);
    }
}
