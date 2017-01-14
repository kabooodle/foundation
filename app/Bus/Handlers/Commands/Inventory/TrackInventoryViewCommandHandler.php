<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Inventory;

use Illuminate\Contracts\Queue\ShouldQueue;
use Kabooodle\Models\User;
use Kabooodle\Models\PageViews;
use Kabooodle\Models\Contracts\ShoppableInterface;
use Kabooodle\Bus\Commands\Inventory\TrackInventoryViewCommand;

/**
 * Class TrackInventoryViewCommandHandler
 */
class TrackInventoryViewCommandHandler implements ShouldQueue
{
    /**
     * @param TrackInventoryViewCommand $command
     *
     * @return PageViews
     */
    public function handle(TrackInventoryViewCommand $command)
    {
        /** @var User $actor */
        $actor = $command->getActor();
        /** @var ShoppableInterface $resource */
        $resource = $command->getResource();
        /** @var string $ip */
        $ip = $command->getIpAddress();

        $insert = PageViews::create([
            'shoppable_type' => get_class($resource),
            'shoppable_id' => $resource->id,
            'inventory_id' => $resource->listedItem->id,
            'ip_address' => $ip,
        ]);

        return $insert;
    }
}
