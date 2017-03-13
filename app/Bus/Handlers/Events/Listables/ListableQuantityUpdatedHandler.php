<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Listables;

use Illuminate\Bus\Queueable;
use Kabooodle\Models\Contracts\ListableInterface;
use Kabooodle\Models\Watches;
use Kabooodle\Models\ListingItems;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Events\Listables\ListableQuantityUpdatedEvent;
use Kabooodle\Bus\Commands\Watchable\NotifyWatcherListableQuantityUpdatedCommand;

/**
 * This handler is queued.
 *
 * Class ListableQuantityUpdatedHandler
 */
class ListableQuantityUpdatedHandler implements ShouldQueue
{
    use DispatchesJobs, Queueable, SerializesModels;

    /**
     * @param ListableQuantityUpdatedEvent $event
     *
     * @return bool
     */
    public function handle(ListableQuantityUpdatedEvent $event)
    {
        $listableItem = $event->getListableItem();

        if ($this->checkIfQuantityChangedFromZero($listableItem)) {
            $this->handleItemWhoseQuantityChangedFromZero($listableItem);
        }

        return true;
    }

    /**
     * @param ListableInterface $listableItem
     *
     * @return bool
     */
    public function handleItemWhoseQuantityChangedFromZero(ListableInterface $listableItem)
    {
        $listings = $this->getListingsForItem($listableItem);
        if ($listings) {
            // Will hold a collection of listings still claimable with watchers.
            $listings = $this->reduceListingsToStillClaimableWithWatchers($listings);
            /** @var ListingItems $listing */
            foreach($listings as $listing) {
                /** @var Watches $watcher */
                foreach ($listing->watchers as $watcher) {
                    $job = new NotifyWatcherListableQuantityUpdatedCommand($watcher->watcher, $listing);
                    $this->dispatch($job);
                }
            }
        }

        return true;
    }

    /**
     * @param ListableInterface $model
     *
     * @return bool
     */
    public function checkIfQuantityChangedFromZero(ListableInterface $model)
    {
        $originalQuantity = $model->getOriginal('initial_qty');

        return $originalQuantity == 0 && $model->initial_qty > 0;
    }

    /**
     * @param ListableInterface $listableItem
     *
     * @return bool|ListingItems
     */
    public function getListingsForItem(ListableInterface $listableItem)
    {
        $listings = $listableItem->listings;

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
