<?php

namespace Kabooodle\Bus\Jobs;

use Exception;
use Carbon\Carbon;
use Kabooodle\Models\Listings;
use Kabooodle\Models\ListingItems;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class EnqueueScheduleListingItemJob
 */
class EnqueueScheduleListingItemJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

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

    public function handle()
    {
        // Cached timestamp of now.
        $timestamp = Carbon::now();

        /** @var ListingItems $listingItem */
        $listingItem = $this->listingItem;

        // Post to FACEBOOK.
        // HERE
        try {

        } catch (Exception $e) {}


        // Update the status
        $this->updateListingsStatus([$listingItem->id], $timestamp, ListingItems::STATUS_SUCCESS);

        return;
    }

    /**
     * @param array $listingIds
     * @param Carbon $timestamp
     * @param string $status
     * @return bool|int
     */
    public function updateListingsStatus(array $listingIds, Carbon $timestamp, $status = Listings::STATUS_QUEUED_LIST)
    {
        return ListingItems::whereIn('id', $listingIds)
            ->update([
                'status' => $status,
                'status_updated_at' => $timestamp->format('Y-m-d H:i:s')
            ]);
    }
}