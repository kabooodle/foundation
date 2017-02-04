<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\InventoryGroupingGrouping;

use Illuminate\Bus\Queueable;
use Kabooodle\Models\Watches;
use Kabooodle\Models\InventoryGrouping;
use Kabooodle\Models\ListingItems;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Events\InventoryGroupings\InventoryGroupingQuantityUpdatedEvent;
use Kabooodle\Bus\Commands\Watchable\NotifyWatcherInventoryGroupingQuantityUpdatedCommand;

/**
 * This handler is queued.
 *
 * Class InventoryGroupingQuantityUpdatedHandler
 */
class InventoryGroupingQuantityUpdatedHandler implements ShouldQueue
{
    use DispatchesJobs, Queueable, SerializesModels;

    /**
     * @param InventoryGroupingQuantityUpdatedEvent $event
     *
     * @return bool
     */
    public function handle(InventoryGroupingQuantityUpdatedEvent $event)
    {
        $grouping = $event->getGrouping();

        if ($this->checkIfQuantityChangedFromZero($grouping)) {
            $this->handleItemWhoseQuantityChangedFromZero($grouping);
        }

        return true;
    }

    /**
     * @param InventoryGrouping $grouping
     *
     * @return bool
     */
    public function handleItemWhoseQuantityChangedFromZero(InventoryGrouping $grouping)
    {
        $listings = $this->getListingsForItem($grouping);
        if ($listings) {
            // Will hold a collection of listings still claimable with watchers.
            $listings = $this->reduceListingsToStillClaimableWithWatchers($listings);
            /** @var ListingItems $listing */
//            foreach($listings as $listing) {
//                /** @var Watches $watcher */
//                foreach ($listing->watchers as $watcher) {
//                    $job = new NotifyWatcherInventoryGroupingQuantityUpdatedCommand($watcher->watcher, $listing);
//                    $this->dispatch($job);
//                }
//            }
        }

        return true;
    }

    /**
     * @param InventoryGrouping $model
     *
     * @return bool
     */
    public function checkIfQuantityChangedFromZero(InventoryGrouping $model)
    {
        $originalQuantity = $model->getOriginal('initial_qty');

        return $originalQuantity == 0 && $model->initial_qty > 0;
    }

    /**
     * @param InventoryGrouping $grouping
     *
     * @return bool|ListingItems
     */
    public function getListingsForItem(InventoryGrouping $grouping)
    {
        $listings = $grouping->listings;

        return $listings->count() > 0 ? $listings : false;
    }

    /**
     * @param $listings
     *
     * @return mixed
     */
    public function reduceListingsToStillClaimableWithWatchers($listings)
    {
        return $listings->filter(function(ListingItems $listing){
            return $listing->claimableBasedOnSchedule() && $listing->watchers->count() > 0;
        });
    }
}
