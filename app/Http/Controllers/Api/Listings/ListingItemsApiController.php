<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Listings;

use Exception;
use Illuminate\Http\Request;
use Kabooodle\Bus\Commands\Listings\ScheduleFacebookListingItemForDeletionCommand;
use Kabooodle\Foundation\Exceptions\FacebookTokenInvalidException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Bus\Commands\Listings\DeleteListingItemCommand;
use Kabooodle\Bus\Events\Listings\FacebookListingItemWasDeleted;
use Kabooodle\Services\Listings\ListingsService;

/**
 * Class ListingItemsApiController
 */
class ListingItemsApiController extends AbstractApiController
{
    use DispatchesJobs;

    /**
     * @var ListingsService
     */
    public $listingService;

    /**
     * @param ListingsService $listingsService
     */
    public function __construct(ListingsService $listingsService)
    {
        $this->listingService = $listingsService;
    }

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
            $this->listingService->assertFacebookAccessTokenIsValid($this->getUser());

            $job = new ScheduleFacebookListingItemForDeletionCommand($this->getUser(), $itemId);
            $item = $this->dispatch($job);
            return $this->setData([
                'msg' => 'Item was successfully queued for deletion from Facebook.',
                'html' => view('listings.partials._detailedrow')->with(compact('item'))->render()
            ])->respond();
        } catch (FacebookTokenInvalidException $e) {
            return $this->setData([
                'msg' => trans('alerts.listings.facebook_token_invalid')
            ])->setStatusCode(401)->respond();
        } catch (Exception $e) {
            return $this->setData([
                'msg' => trans('alerts.error_generic_retry')
            ])->setStatusCode(500)->respond();
        }
    }
}
