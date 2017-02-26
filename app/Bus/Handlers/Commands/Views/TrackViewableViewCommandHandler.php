<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Views;

use Illuminate\Contracts\Queue\ShouldQueue;
use Kabooodle\Models\Contracts\Viewable;
use Kabooodle\Models\User;
use Kabooodle\Models\View;
use Kabooodle\Bus\Commands\Views\TrackViewableViewCommand;

/**
 * Class TrackViewableViewCommandHandler
 */
class TrackViewableViewCommandHandler implements ShouldQueue
{
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

        return $view;
    }
}
