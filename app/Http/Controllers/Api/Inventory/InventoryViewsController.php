<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Inventory;

use Binput;
use Exception;
use Illuminate\Http\Request;
use Kabooodle\Models\Traits\ShoppableTrait;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Bus\Commands\Inventory\TrackInventoryViewCommand;

/**
 * Class InventoryViewsController
 */
class InventoryViewsController extends AbstractApiController
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

            $job = with(new TrackInventoryViewCommand($user, $resource, $ip))
                ->delay(120)
                ->onConnection('database');

            $this->dispatch($job);

            return $this->noContent();
        } catch (Exception $e) {
            dd($e);
            return $this->setStatusCode(500)->respond();
        }
    }
}
