<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Listings;

use Binput;
use Bugsnag;
use Exception;
use Illuminate\Http\Request;
use Kabooodle\Bus\Commands\Listings\ScheduleFacebookListingCommand;
use Kabooodle\Bus\Commands\Listings\ScheduleFlashsaleListingCommand;
use Kabooodle\Models\Listings;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Models\Listing\FacebookListingOptions;
use Kabooodle\Http\Controllers\Traits\PaginatesTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Facebook\Exceptions\FacebookAuthenticationException;
use Kabooodle\Bus\Commands\Listings\DeleteListingCommand;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Kabooodle\Foundation\Exceptions\Listings\ListingConflictsWithExistingListingException;
use Kabooodle\Foundation\Exceptions\Listings\ListingClaimableDateIsBeforeListingDateException;

/**
 * Class ListingsApiController
 */
class ListingsApiController extends AbstractApiController
{
    use DispatchesJobs, PaginatesTrait;

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $listings = $this->getUser()->listings;

        return $this->setData($listings)->respond();
    }

    /**
     * @param Request $request
     * @param string  $listingUuid
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $listingUuid)
    {
        try {
            $listing = Listings::with(['items.listedItem'])
                ->where('uuid', $listingUuid)
                ->orderBy('scheduled_for')
                ->first();

            if (! $listing) {
                throw new ModelNotFoundException;
            }

            $listing->loadItemsListedItem();

            $items = $listing->items;

            $style_query = Binput::get('styles', false);
            $size_query = Binput::get('sizes', false);
            $sellers_query = Binput::get('sellers', false);

            if ($style_query ) {
                $items = $items->filter(function($item) use ($style_query){
                    return in_array($item->listedItem->inventory_type_styles_id, $style_query);
                });
            }

            if ($size_query ) {
                $items = $items->filter(function($item) use ($size_query){
                    return in_array($item->listedItem->inventory_sizes_id, $size_query);
                });
            }

            if ($sellers_query) {
                $items = $items->filter(function($item) use ($sellers_query){
                    return in_array($item->owner_id, $sellers_query);
                });
            }

            $items = $items->sortBy(function($item){
                return $item->make_available_at;
            })->sortBy(function($item){
                return $item->id;
            })->values();

            $items = $this->paginateData($request, $items);

            return $this->setData($items)->respond();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)->respond();
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     * @throws ListingClaimableDateIsBeforeListingDateException
     */
    public function store(Request $request)
    {
        $facebooksales = Binput::get('facebooksales', []);
        $flashsales = Binput::get('flashsales', []);
        $facebookOptions = (array) Binput::get('options', []);
        $facebooksales_meta = Binput::get('facebooksales_meta', null);

        // Date to list it and remove it
        $listAt = array_get($options, 'list_at', null);
        $removeAt = array_get($options, 'remove_at', null);
        // Date range you can claim.
        $claimableAt = array_get($options, 'available_at', null);
        $claimableUntil = array_get($options, 'available_until', null);
        $itemMessage = array_get($options, 'item_message', false);

        try {
            //
            if ($claimableAt && strtotime($claimableAt) < strtotime($listAt)) {
                throw new ListingClaimableDateIsBeforeListingDateException('The earliest date an item can be claimed cannot come before the listing date.');
            }

            if ($facebookOptions && $facebooksales) {
                $listingOptions = new FacebookListingOptions($listAt, $removeAt, $claimableAt, $claimableUntil, $itemMessage);
//                $job = new ScheduleFacebookListingCommand($this->getUser(), );
//                $this->dispatchNow($job);
            }

            if ($flashsales) {
//                $job = new ScheduleFlashsaleListingCommand($this->getUser(), );
//                $this->dispatchNow($job);
            }

            return $this->setData(['msg' => 'Items scheduled successfully to sale.'])->respond();
        } catch (FacebookAuthenticationException $e) {
            $msg = 'Your facebook credentials are invalid. Please re-authorize ' . env('APP_NAME') . ' for your facebook account, via our settings page.';

            return $this->setData(['msg' => $msg])->setStatusCode(500)->respond();
        } catch (MissingMandatoryParametersException $e) {
            return $this->setStatusCode(500)->setData(['msg' => $e->getMessage() ?: 'You must select as least 1 item for listing.'])->respond();
        } catch (ListingConflictsWithExistingListingException $e) {
            return $this->setStatusCode(500)->setData(['msg' => 'The date and time block you selected conflicts with an existing listing. Please select a new block of time.'])->respond();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);

            return $this->setStatusCode(500)->setData(['msg' => $e->getMessage()])->respond();
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $job = new DeleteListingCommand($this->getUser(), $id);
            $this->dispatchNow($job);

            return $this->setData([
                'msg' => trans('alerts.listings.success_listing_deleted')
            ])->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)->setData([
                'msg' => trans('alerts.error_generic_retry')
            ])->respond();
        }
    }
}
