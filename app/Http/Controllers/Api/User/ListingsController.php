<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\User;

use DB;
use Binput;
use Illuminate\Http\Request;
use Kabooodle\Models\Listable;
use Kabooodle\Models\Listings;
use Kabooodle\Models\ListingItems;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Models\User;
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
        $actor = $this->getUser();
        $owner = User::where('username', '=', $username)->firstOrFail();
        $listings = Listings::noAppends()->noEagerLoads()->with(['morphedType', 'items'])
            ->where('owner_id', '=', $owner->id)
            ->orderBy('scheduled_for', 'asc')
            ->paginate(config('pagination.per-page'));

        // Filter through the items and hide private items where the user is not
        // a seller.  Reminder, sellers include admins, owner, sellers.
        $listings->setCollection($listings->filter(function (Listings $listing) use ($actor) {
            return $listing->isFlashsale() ? $listing->flashSale->canUserViewPrivateSale($actor) : $listing;
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
        $owner = User::where('username', '=', $username)->firstOrFail();
        $listings = ListingItems::noEagerLoads()->with(['listing','owner' => function($q) use ($username) {
            $q->where('username', '=', Binput::clean($username));
        }])
            ->where('owner_id', '=', $owner->id)
            ->get();

        $listing = $listings->first()->listing;

        return $this->setData(['listings' => $listings, 'listing' => $listing])->respond();
    }
}