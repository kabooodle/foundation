<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\InventoryGroupings;

use Binput;
use Exception;
use Illuminate\Http\Request;
use Kabooodle\Libraries\QueueHelper;
use Kabooodle\Models\Traits\ShoppableTrait;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Bus\Commands\Views\TrackViewableViewCommand;

/**
 * Class InventoryGroupingsViewsController
 */
class InventoryGroupingsViewsController extends AbstractApiController
{
    use ShoppableTrait;

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $resourceHash = Binput::get('resource');
            list($resource, $resourceId) = $this->decryptHashedResource($resourceHash);
            $resource = $resource::findOrFail($resourceId);

            $ip = $request->getClientIp();
            $user = $this->getUser();

            $job = new TrackViewableViewCommand($user, $resource, $ip);
            $job->onConnection(QueueHelper::pickViewTracker())
                ->delay(60);

            $this->dispatch($job);

            return $this->noContent();
        } catch (Exception $e) {
            return $this->setStatusCode(500)->respond();
        }
    }
}
