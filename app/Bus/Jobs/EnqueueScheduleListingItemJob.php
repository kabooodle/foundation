<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Jobs;

use DB;
use Bugsnag;
use Exception;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Kabooodle\Foundation\Exceptions\FacebookTokenInvalidException;
use Kabooodle\Models\Files;
use Kabooodle\Models\Listings;
use Kabooodle\Models\Queues;
use Kabooodle\Models\ListingItems;
use Illuminate\Contracts\Queue\ShouldQueue;
use Facebook\Exceptions\FacebookThrottleException;
use Kabooodle\Services\Listings\ListingsService;
use Kabooodle\Services\Social\Facebook\FacebookSdkService;
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

            $facebook = $this->getFacebookService();

            $listingItem = $this->getListingItem();

            // Update the listing item status as "processing"
            $this->updateQueueStatus($this->queuesId, $this->timestamp, Queues::STATUS_PROCESSING, $this->job->attempts());

            // Throws exception is token is invalid.
            // This is handled as a successful job, because we dont want to repeat the job over and over.
            // TODO: Is this a call?
//            $this->getListingService()->assertFacebookAccessTokenIsValid($listingItem->owner);

            $facebookParams = $this->buildFacebookAlbumParams($listingItem);

            $response = $facebook->postPhotoToGroupAlbum(
                $listingItem->fb_album_node_id,
                $facebookParams,
                $listingItem->owner->getFacebookUserToken()
            );

            $response = $response->asArray();

//            event(new ListingItemWasListed);

            $this->successfulJobHandler($listingItem, ['fb_response_object_id' => $response['id']]);

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

        $remainingListingItems = $this->getRemainingListingItems($listingItem->listing_id);

        // TODO: I added <= 5 as a buffer because 100% isn't the best. Figure out algorithm to better identify accuracy
        if (! $remainingListingItems || count($remainingListingItems) == 0 || count($remainingListingItems) <= 5) {
            // Update the status to the appropriate status based on the result.
            $this->updateListingsStatus([$listingItem->listing_id], $this->timestamp, Listings::STATUS_COMPLETED);
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

    /**
     * @param       $listingItem
     * @param array $attributes
     */
    public function successfulJobHandler($listingItem, array $attributes = [])
    {
        // Update the status to the appropriate status based on the result.
        $this->updateListingItemsStatus([$listingItem->id], $this->timestamp, ListingItems::STATUS_SUCCESS, $attributes);

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
        $photoMessage = $listingItem->parseItemMessage();

        $image = $this->getListingImage($listingItem);

        if (!$image) {
            throw new ListingPhotoMissingException($listingItem->id);
        }

        return [
            'url' => $image->getOriginal('location'),
            'message' => $photoMessage ? : null
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function getListingItem()
    {
        return ListingItems::findOrFail($this->listingItemId);
    }

    /**
     * @param ListingItems $listingItem
     *
     * @return Files
     */
    public function getListingImage(ListingItems $listingItem)
    {
        return $listingItem->listedItem->cover_photo ? : $listingItem->listedItem->firstImage();
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

    /**
     * @return \Illuminate\Foundation\Application|ListingsService|mixed
     */
    public function getListingService()
    {
        return app(ListingsService::class);
    }

    /**
     * @return \Illuminate\Foundation\Application|FacebookSdkService|mixed
     */
    public function getFacebookService()
    {
        return app(\Kabooodle\Services\Social\Facebook\FacebookSdkService::class);
    }
}
