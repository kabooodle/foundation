<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Views;

use Bugsnag;
use Kabooodle\Models\User;
use Kabooodle\Models\View;
use Kabooodle\Models\ListingItems;
use Kabooodle\Models\InventoryGrouping;
use Kabooodle\Models\Contracts\Viewable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Views\TrackViewableViewCommand;

/**
 * Class TrackViewableViewCommandHandler
 */
class TrackViewableViewCommandHandler implements ShouldQueue
{
    use DispatchesJobs;

    /**
     * @param TrackViewableViewCommand $command
     *
     * @return View
     */
    public function handle(TrackViewableViewCommand $command)
    {
        /** @var User $actor */
        $actor = $command->getActor();
        /** @var Viewable $resource */
        $resource = $command->getResource();
        /** @var string $ip */
        $ip = $command->getIpAddress();

        $view = View::create([
            'viewer_id' => $actor ? $actor->id : null,
            'viewable_type' => get_class($resource),
            'viewable_id' => $resource->id,
            'ip_address' => $ip,
        ]);

        $this->completedCallback($command);

        return $view;
    }

    /**
     * @param TrackViewableViewCommand $command
     */
    public function completedCallback(TrackViewableViewCommand $command)
    {
        $resource = $command->getResource();
        $resourceName =  get_class($resource);

        try {
            if ($resourceName == ListingItems::class) {
                // I guess  individual listed items' inventory is tracked as a view already somehow
//                $job = new TrackViewableViewCommand(
//                    $command->getActor(),
//                    $resource->listedItem,
//                    $command->getIpAddress()
//                );
//
//                $this->dispatchNow($job);
            } elseif ($resourceName == InventoryGrouping::class) {
                foreach ($resource->inventoryItems as $inventoryItem) {
                    $job = new TrackViewableViewCommand(
                        $command->getActor(),
                        $inventoryItem,
                        $command->getIpAddress()
                    );

                    $this->dispatchNow($job);
                }
            }
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }
    }
}
