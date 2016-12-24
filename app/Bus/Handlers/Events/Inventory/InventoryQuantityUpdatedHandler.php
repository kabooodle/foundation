<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Inventory;

use Illuminate\Bus\Queueable;
use Kabooodle\Models\Watches;
use Kabooodle\Models\Inventory;
use Kabooodle\Models\ListingItems;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Events\Inventory\InventoryQuantityUpdatedEvent;
use Kabooodle\Bus\Commands\Watchable\NotifyWatcherInventoryQuantityUpdatedCommand;

/**
 * This handler is queued.
 *
 * Class InventoryQuantityUpdatedHandler
 */
class InventoryQuantityUpdatedHandler implements ShouldQueue
{
    use DispatchesJobs, Queueable, SerializesModels;

    /**
     * @param InventoryQuantityUpdatedEvent $event
     *
     * @return bool
     */
    public function handle(InventoryQuantityUpdatedEvent $event)
    {
        $inventoryItem = $event->getInventoryItem();

        if ($this->checkIfQuantityChangedFromZero($inventoryItem)) {
            $this->handleItemWhoseQuantityChangedFromZero($inventoryItem);
        }

        return true;
    }

    /**
     * @param Inventory $inventoryItem
     *
     * @return bool
     */
    public function handleItemWhoseQuantityChangedFromZero(Inventory $inventoryItem)
    {
        $listings = $this->getListingsForItem($inventoryItem);
        if ($listings) {
            // Will hold a collection of listings still claimable with watchers.
            $listings = $this->reduceListingsToStillClaimableWithWatchers($listings);
            /** @var ListingItems $listing */
            foreach($listings as $listing) {
                /** @var Watches $watcher */
                foreach ($listing->watchers as $watcher) {
                    $job = new NotifyWatcherInventoryQuantityUpdatedCommand($watcher->watcher, $listing);
                    $this->dispatch($job);
                }
            }
        }

        return true;
    }

    /**
     * @param Inventory $model
     *
     * @return bool
     */
    public function checkIfQuantityChangedFromZero(Inventory $model)
    {
        $originalQuantity = $model->getOriginal('initial_qty');

        return $originalQuantity == 0 && $model->initial_qty > 0;
    }

    /**
     * @param Inventory $inventoryItem
     *
     * @return bool|ListingItems
     */
    public function getListingsForItem(Inventory $inventoryItem)
    {
        $listings = $inventoryItem->listings;

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
