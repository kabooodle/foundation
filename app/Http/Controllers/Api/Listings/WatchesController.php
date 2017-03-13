<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Listings;

use Exception;
use Illuminate\Http\Request;
use Kabooodle\Models\ListingItems;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Bus\Commands\Watchable\UnwatchEntityCommand;
use Kabooodle\Bus\Commands\Watchable\WatchNewEntityCommand;

/**
 * Class WatchesController
 */
class WatchesController extends AbstractApiController
{
    use DispatchesJobs;

    /**
     * @param Request $request
     * @param int     $listingId
     * @param int     $listingItemId
     *
     * @return \Dingo\Api\Http\Response
     */
    public function store(Request $request, $listingId, $listingItemId)
    {
        $user = $this->getUser();

        try {
            $watchable = ListingItems::where('listing_id', $listingId)->where('id', $listingItemId)->first();
            if (!$watchable) {
                throw new ModelNotFoundException;
            }

            $this->dispatchNow(new WatchNewEntityCommand($user, $watchable));

            return $this->noContent();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)->respond();
        }
    }

    /**
     * @param Request $request
     * @param int     $listingId
     * @param int     $listingItemId
     *
     * @return \Dingo\Api\Http\Response
     */
    public function destroy(Request $request, $listingId, $listingItemId)
    {
        $user = $this->getUser();
        try {
            $watchable = ListingItems::where('listing_id', $listingId)->where('id', $listingItemId)->first();
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
