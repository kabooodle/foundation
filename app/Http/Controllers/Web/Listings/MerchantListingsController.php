<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Listings;

use Illuminate\Http\Request;
use Kabooodle\Models\Listings;
use Kabooodle\Http\Controllers\Web\Controller;

/**
 * Class MerchantListingsController
 */
class MerchantListingsController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $listings = Listings::getQueriedListings(user()->id);

        return $this->view('listings.merchant.index')->with(compact('listings'));
    }

    /**
     * @param Request $request
     * @param         $uuid
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, $uuid)
    {
        $listing = user()->listings->where('uuid', $uuid)->first();
        if (!$listing) {
            return redirect()->to(route('merchant.listings.index'));
        }

        $listings = $listing->listingsGroupedByItemTypeGrouping(user()->id);

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
        $listing = user()->listings->where('uuid', $uuid)->first();
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
        $listing = user()->listings->where('uuid', $uuid)->first();
        if (!$listing) {
            return redirect()->to(route('merchant.listings.index'));
        }
        $listings = $listing->listingsGroupedByItemTypeGrouping(user()->id);

        return $this->view('listings.merchant.show')->with(compact('listing', 'listings'));
    }

    /**
     * @param Request $request
     * @param         $uuid
     * @param         $type
     * @param         $id
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function detailed(Request $request, $uuid, $type, $id)
    {
        $listing = Listings::with(['listingItems' => function ($query) use ($type, $id) {
            if ($type == Listings::TYPE_FACEBOOK) {
                $query->where('fb_album_node_id', '=', $id);
            } elseif ($type == Listings::TYPE_FLASHSALE) {
                $query->where('flashsale_id', '=',  $id);
            }
        }])
            ->where('uuid', $uuid)
            ->where('owner_id', user()->id)
            ->where('type', $type)
            ->first();
        if (!$listing) {
            return redirect()->to(route('merchant.listings.index'));
        }

        return $this->view('listings.merchant.detailed')->with(compact('listing'));
    }
}
