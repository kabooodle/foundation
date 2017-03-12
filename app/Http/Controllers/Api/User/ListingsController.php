<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\User;

use DB;
use Binput;
use Illuminate\Http\Request;
use Kabooodle\Models\Listable;
use Kabooodle\Models\Listings;
use Kabooodle\Models\ListingItems;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Transformers\Listings\UserListingsTransformer;

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
        $user = $this->getUser();
        $listings = Listings::noEagerLoads()->with(['morphedType', 'items','owner' => function($q) use ($username) {
            $q->where('username', '=', Binput::clean($username));
        }])
//            ->where('scheduled_for', '>=', DB::raw('NOW()'))
            ->orderBy('scheduled_for', 'asc')
            ->paginate(config('pagination.per-page'));

        // Filter through the items and hide private items where the user is not
        // a seller.  Reminder, sellers include admins, owner, sellers.
        $listings->setCollection($listings->filter(function (Listings $listing) use ($user) {
            return $listing->isFlashsale() ? $listing->flashSale->canUserViewPrivateSale($user) : $listing;
        }));

        return $this->response->paginator($listings, new UserListingsTransformer);
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