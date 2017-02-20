<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Listings;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Kabooodle\Models\ListingItems;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Bus\Commands\Listings\DeleteListingItemCommand;
use Kabooodle\Bus\Events\Listings\FacebookListingItemWasDeleted;
use Kabooodle\Bus\Commands\Listings\DeleteListingItemFromFacebookCommand;

/**
 * Class ListingItemsApiController
 */
class ListingItemsApiController extends AbstractApiController
{
    use DispatchesJobs;

    /**
     * @param Request $request
     * @param         $listingId
     * @param         $itemId
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $listingId, $itemId)
    {
        try {
            $job = new DeleteListingItemCommand($this->getUser(), $listingId, $itemId);
            $this->dispatchNow($job);

            return $this->setData([
                'msg' => 'Item was successfully deleted.'
            ])->respond();
        } catch (Exception $e) {
            return $this->setData([
                'msg' => trans('alerts.error_generic_retry')
            ])->setStatusCode(500)->respond();
        }
    }

    /**
     * @param Request $request
     * @param         $listingId
     * @param         $itemId
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyFromFacebook(Request $request, $listingId, $itemId)
    {
        try {
            $item = ListingItems::where('listing_id', '=', $listingId)
                ->where('id', '=', $itemId)
                ->where('owner_id', '=', $this->getUser()->id)
                ->where('status', '<>', ListingItems::STATUS_QUEUED_DELETE)
                ->firstOrFail();

            $item->status = ListingItems::STATUS_QUEUED_DELETE;
            $item->status_updated_at = Carbon::now();
            $item->save();

            $job = new DeleteListingItemFromFacebookCommand($this->getUser()->id, $listingId, $itemId);
            $this->dispatch($job);

            return $this->setData([
                'msg' => 'Item was successfully queued for deletion from Facebook.',
                'html' => view('listings.partials._detailedrow')->with(compact('item'))->render()
            ])->respond();
        } catch (Exception $e) {
            return $this->setData([
                'msg' => trans('alerts.error_generic_retry')
            ])->setStatusCode(500)->respond();
        }
    }
}
