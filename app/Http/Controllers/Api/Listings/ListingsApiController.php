<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Listings;

use Binput;
use Bugsnag;
use Exception;
use Illuminate\Http\Request;
use Kabooodle\Bus\Commands\Listings\CheckFacebookListingsQuotaForPeriod;
use Kabooodle\Bus\Commands\Listings\PrepareDeleteListingFromFacebookCommand;
use Kabooodle\Bus\Commands\Listings\ScheduleFacebookListingCommand;
use Kabooodle\Bus\Commands\Listings\ScheduleFacebookListingDeletionCommand;
use Kabooodle\Bus\Commands\Listings\ScheduleFlashsaleListingCommand;
use Kabooodle\Bus\Commands\Listings\ScheduleNewListingsCommand;
use Kabooodle\Foundation\Exceptions\FacebookTokenExpiredException;
use Kabooodle\Foundation\Exceptions\FacebookTokenInvalidException;
use Kabooodle\Foundation\Exceptions\Listings\ListingExceedsHourlyLimitException;
use Kabooodle\Http\Controllers\Traits\FilterListingItemsTrait;
use Kabooodle\Models\Listings;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Models\Listing\FacebookListingOptions;
use Kabooodle\Http\Controllers\Traits\PaginatesTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Facebook\Exceptions\FacebookAuthenticationException;
use Kabooodle\Bus\Commands\Listings\DeleteListingCommand;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Services\Listings\ListingsService;
use Kabooodle\Services\User\UserService;
use Kabooodle\Transformers\Listings\ListingItemsTransformer;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Kabooodle\Foundation\Exceptions\Listings\ListingConflictsWithExistingListingException;
use Kabooodle\Foundation\Exceptions\Listings\ListingClaimableDateIsBeforeListingDateException;

/**
 * Class ListingsApiController
 */
class ListingsApiController extends AbstractApiController
{
    use DispatchesJobs, PaginatesTrait, FilterListingItemsTrait;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var ListingsService
     */
    protected $listingService;

    /**
     * @param UserService     $userService
     * @param ListingsService $listingsService
     */
    public function __construct(UserService $userService, ListingsService $listingsService)
    {
        $this->userService = $userService;
        $this->listingService = $listingsService;
    }

    /**
     * @param Request $request
     *
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

            if (!$listing) {
                throw new ModelNotFoundException;
            }

            $items = $listing->items;

            $style_query = Binput::get('styles', []);
            $size_query = Binput::get('sizes', []);
            $sellers_query = Binput::get('sellers', []);

            $items = $this->filterListingItems($items, $style_query, $size_query, $sellers_query);

            return $this->response->paginator($this->paginateData($request, $items), new ListingItemsTransformer);
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
        $facebooksales = Binput::get('facebooksales', []); // All the facebook listings
        $flashsales = Binput::get('flashsales', []); // All the flash sale listings

        $facebookOptions = (array) Binput::get('options', []); // All the facebook options
        $facebooksales_meta = Binput::get('facebooksales_meta', null); // Facebook listings meta

        // Date to list it and remove it
        $listAt = array_get($facebookOptions, 'list_at', null);
        $removeAt = array_get($facebookOptions, 'remove_at', null);
        // Date range you can claim.
        $claimableAt = array_get($facebookOptions, 'available_at', null);
        $claimableUntil = array_get($facebookOptions, 'available_until', null);
        $itemMessage = array_get($facebookOptions, 'item_message', false);

        try {
            $facebookListings = null;
            $flashsaleListings = null;

            if ($facebookOptions && $facebooksales) {
                // Build our Facebook Listing Options object
                $listingOptions = new FacebookListingOptions($listAt, $removeAt, $claimableAt, $claimableUntil, $itemMessage);

                // Make sure access token is valid.
                $this->listingService->assertFacebookAccessTokenIsValid($this->getUser());

                // claimable date cannot be before the listing is event scheduled
                $this->listingService->assertListingClaimableDateIsBeforeListingDateException($claimableAt, $listAt);

                // Check the listings does not exceed the hourly quota
                $this->listingService->assertNumberOfItemsDoesNotExceedFacebookHourlyQuota(
                    $this->getUser(),
                    $listingOptions->getStartsAt()->toDateTimeString(),
                    (int) $facebooksales_meta['total_listables']
                );

                // FacebookListingsJob
                $facebookListings = new ScheduleFacebookListingCommand($this->getUser(), $listingOptions, $facebooksales);
            }

            if ($flashsales) {
                $flashsaleListings = new ScheduleFlashsaleListingCommand($this->getUser(), $flashsales);
            }

            $this->dispatchNow(new ScheduleNewListingsCommand($this->getUser(), $facebookListings, $flashsaleListings));

            return $this->setData(['msg' => 'Listings successfully scheduled!'])->respond();
        } catch (FacebookAuthenticationException $e) {
            $msg = 'Your facebook credentials are invalid. Please re-authorize ' . env('APP_NAME') . ' for your facebook account, via our settings page.';

            return $this->setData(['msg' => $msg])->setStatusCode(500)->respond();
        } catch (ListingExceedsHourlyLimitException $e) {
            return $this->setStatusCode(500)->setData([
                'msg' => trans('alerts.listings.listings_exceeds_hourly_limit',
                    [
                        'allowed' => $e->getMaximumTotalAllowedForHour(),
                        'current'=> $e->getTotalExistingListingsForHour()
                    ])
            ])->respond();
        } catch (MissingMandatoryParametersException $e) {
            return $this->setStatusCode(500)->setData(['msg' => $e->getMessage() ?: 'You must select as least 1 item for listing.'])->respond();
        } catch (FacebookTokenInvalidException $e) {
            return $this->setStatusCode(401)->setData(['msg' => trans('alerts.listings.facebook_token_invalid')])->respond();
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

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyFromFacebook(Request $request, $id)
    {
        try {
            $this->listingService->assertFacebookAccessTokenIsValid($this->getUser());

            $job = new ScheduleFacebookListingDeletionCommand($this->getUser(), $id);
            $listing = $this->dispatchNow($job);

            return $this->setData([
                'msg' => trans('alerts.listings.success_listing_fb_deleted'),
                'html' => view('listings.partials._indexrow')->with(compact('listing'))->render()
            ])->respond();
        } catch (FacebookTokenInvalidException $e) {
            return $this->setData([
                'msg' => trans('alerts.listings.facebook_token_invalid')
            ])->setStatusCode(401)->respond();
        } catch (Exception $e) {
            dd($e->getMessage());
            return $this->setStatusCode(500)->setData([
                'msg' => trans('alerts.error_generic_retry')
            ])->respond();
        }
    }
}
