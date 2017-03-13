<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Listings;

use Illuminate\Http\Request;
use Kabooodle\Models\Listings;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Controllers\Web\Controller;

/**
 * Class MerchantListingsController
 */
class MerchantListingsController extends Controller
{
    use DispatchesJobs;
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $listings = Listings::getQueriedListings(webUser()->id);

        return $this->view('listings.merchant.index')->with(compact('listings'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create(Request $request)
    {
        return $this->view('listings.merchant.create');
    }

    /**
     * @param Request $request
     * @param         $uuid
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, $uuid)
    {
        $listing = webUser()->listings->where('uuid', $uuid)->first();
        if (!$listing) {
            return redirect()->to(route('merchant.listings.index'));
        }

        $listings = $listing->listingsGroupedByItemTypeGrouping(webUser()->id);

        return $this->view('listings.merchant.edit')->with(compact('listing', 'listings'));
    }

    /**
     * @param Request $request
     * @param         $uuid
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $uuid)
    {
        $listing = webUser()->listings->where('uuid', $uuid)->first();
        if (!$listing) {
            return redirect()->to(route('merchant.listings.index'));
        }
    }

    /**
     * @param Request $request
     * @param $uuid
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Request $request, $uuid)
    {
        $listing = webUser()->listings->where('uuid', $uuid)->first();
        if (!$listing) {
            return redirect()->to(route('merchant.listings.index'));
        }
        $listings = $listing->listingsGroupedByItemTypeGrouping(webUser()->id);

        return $this->view('listings.merchant.show')->with(compact('listing', 'listings'));
    }

    /**
     * @param Request $request
     * @param         $listingUuid
     * @param         $type
     * @param         $id
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function detailed(Request $request, $listingUuid, $type, $id = null)
    {
        $listing = Listings::with(['listingItems' => function ($query) use ($type, $id) {
            if ($type == Listings::TYPE_FACEBOOK) {
                $query->where('fb_album_node_id', '=', $id);
            } elseif ($type == Listings::TYPE_FLASHSALE) {
                $query->where('flashsale_id', '=', $id);
            }
        }])
            ->where('uuid', $listingUuid)
            ->where('owner_id', webUser()->id)
            ->where('type', $type)
            ->first();

        if (!$listing) {
            return redirect()->to(route('merchant.listings.index'));
        }

        return $this->view('listings.merchant.detailed')->with(compact('listing'));
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function store(Request $request)
    {

    }
}
