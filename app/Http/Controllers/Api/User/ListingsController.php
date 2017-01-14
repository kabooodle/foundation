<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\User;

use DB;
use Binput;
use Illuminate\Http\Request;
use Kabooodle\Models\Listings;
use Kabooodle\Models\ListingItems;
use Kabooodle\Http\Controllers\Api\AbstractApiController;

/**
 * Class ListingsController
 */
class ListingsController extends AbstractApiController
{
    /**
     * @param Request $request
     * @param string  $username
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, string $username)
    {
        $listings = Listings::noEagerLoads()->with(['items','owner' => function($q) use ($username) {
            $q->where('username', '=', Binput::clean($username));
        }])
            ->where('scheduled_for', '>=', DB::raw('NOW()'))
            ->orderBy('scheduled_for', 'asc')
            ->paginate(config('pagination.per-page'));

        return $this->setData($listings)->respond();
    }

    /**
     * @param Request $request
     * @param string  $username
     * @param         $listingId
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, string $username, $listingId)
    {
        $listings = ListingItems::noEagerLoads()->with(['listing','owner' => function($q) use ($username) {
            $q->where('username', '=', Binput::clean($username));
        }])->get();

//        $categories = $this->api()->get(apiRoute());
        $listing = $listings->first()->listing;

        return $this->setData(['listings' => $listings, 'listing' => $listing])->respond();
    }
}