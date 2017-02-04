<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Watchable;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kabooodle\Models\User;
use Kabooodle\Models\ListingItems;

/**
 * Class NotifyWatcherInventoryQuantityUpdatedCommand
 */
final class NotifyWatcherInventoryQuantityUpdatedCommand implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var User
     */
    public $watcher;

    /**
     * @var ListingItems
     */
    public $listingItem;

    /**
     * @param User         $watcher
     * @param ListingItems $listingItem
     */
    public function __construct(User $watcher, ListingItems $listingItem)
    {
        $this->watcher = $watcher;
        $this->listingItem = $listingItem;
    }

    /**
     * @return User
     */
    public function getWatcher(): User
    {
        return $this->watcher;
    }

    /**
     * @return ListingItems
     */
    public function getListingItem(): ListingItems
    {
        return $this->listingItem;
    }
}
