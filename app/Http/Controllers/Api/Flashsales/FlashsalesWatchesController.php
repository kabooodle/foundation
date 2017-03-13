<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Flashsales;

use Exception;
use Illuminate\Http\Request;
use Kabooodle\Models\FlashSales;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Bus\Commands\Watchable\UnwatchEntityCommand;
use Kabooodle\Bus\Commands\Watchable\WatchNewEntityCommand;

/**
 * Class WatchesController
 */
class FlashsalesWatchesController extends AbstractApiController
{
    use DispatchesJobs;

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $user = $this->getUser();

        try {
            $watchable = FlashSales::where('id', $id)->first();
            if (!$watchable) {
                throw new ModelNotFoundException;
            }

            $this->dispatchNow(new WatchNewEntityCommand($user, $watchable));

            return $this->noContent();
        } catch (Exception $e) {
            return $this->setStatusCode(500)->respond();
        }
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $user = $this->getUser();
        try {
            $watchable = FlashSales::where('id', $id)->first();
            if (!$watchable) {
                throw new ModelNotFoundException;
            }

            $this->dispatchNow(new UnwatchEntityCommand($user, $watchable));

            return $this->noContent();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)->respond();
        }
    }
}
