<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Views;

use Bugsnag;
use Kabooodle\Models\User;
use Kabooodle\Models\View;
use Kabooodle\Models\ListingItems;
use Kabooodle\Models\InventoryGrouping;
use Kabooodle\Services\Keen\KeenService;
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
     * @var KeenService
     */
    public $keenService;

    /**
     * @param KeenService $keenService
     */
    public function __construct(KeenService $keenService)
    {
        $this->keenService = $keenService;
    }

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

        $this->recordChildViews($command);

        $this->handleKeenTracking($view);

        return $view;
    }

    /**
     * @param TrackViewableViewCommand $command
     */
    public function recordChildViews(TrackViewableViewCommand $command)
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

    /**
     * @param View $view
     */
    public function handleKeenTracking(View $view)
    {
        try {
            $data = [
                'owner' => $view->viewer,
                'raw_object' => $view,
                'viewable' => $view->viewable,
                'viewable_type' => $view->viewable_type,
                'viewable_id' => $view->viewable_id,
                'ip_address' => $view->ip_address,
            ];

            $this->keenService->keenClient->addEvent('entity_views', $data);
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }
    }
}
