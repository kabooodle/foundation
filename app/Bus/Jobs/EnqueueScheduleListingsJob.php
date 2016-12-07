<?php

namespace Kabooodle\Bus\Jobs;

use Carbon\Carbon;
use Kabooodle\Models\Listings;
use Kabooodle\Models\ListingItems;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Class EnqueueScheduleListingsJob
 */
class EnqueueScheduleListingsJob extends Job implements ShouldQueue
{
    use DispatchesJobs, InteractsWithQueue, SerializesModels;

    /**
     * @var Collection
     */
    public $listingModels;

    /**
     * @param Collection $listingModels
     */
    public function __construct(Collection $listingModels)
    {
        $this->listingModels = $listingModels;
    }

    public function handle()
    {
        // Cached timestamp of now.
        $timestamp = Carbon::now();

        // Collection that will contain all the listings' listing items, ignoring their origin.
        $listingItems = collect([]);

        /** @var Collection $listingModels */
        $listingModels = $this->listingModels;

        // We shuffle just to randomize the parent listings and keep our process as random as possible.
        $shuffledListings = $listingModels->shuffle();

        // Update all the listings' status from "scheduled" to "queued"
        // We change this now instead of after its complete because we dont want to create an overlap
        $this->updateListingsStatus($shuffledListings->pluck('id')->toArray(), $timestamp);

        /** @var Listings $listing */
        foreach($shuffledListings as $listing) {

            /** @var ListingItems $item */
            foreach ($listing->listingItems as $item) {

                // Although we currently only care for scheduled items, and its assumed
                // we already only have schedule items, there are duplicates that we need to ignore
                // and perhaps in the future we may need to ignore other status'
                if (in_array($item->status, [ListingItems::STATUS_SCHEDULED])) {

                    // Push the item into the collection of items.
                    $listingItems->push($item);
                }
            }
        }

        // Shuffle all the listing items, similar to above, to keep everything as random as possible.
        $shuffledListingItems = $listingItems->shuffle();

        foreach($shuffledListingItems as $shuffledListingItem) {

            // Add the listing item to the queue
            $this->dispatch(new EnqueueScheduleListingItemJob($shuffledListingItem));
        }

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
        return Listings::whereIn('id', $listingIds)
            ->update([
                'status' => $status,
                'status_updated_at' => $timestamp->format('Y-m-d H:i:s')
            ]);
    }
}