<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Listings;

use Illuminate\Http\Request;
use Kabooodle\Models\Listings;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Http\Controllers\Traits\PaginatesTrait;

/**
 * Class ListingItemsController
 */
class ListingsController extends Controller
{
    use PaginatesTrait;

    /**
     * @param Request $request
     * @param         $listingUuid
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function shorthand(Request $request, $listingUuid)
    {
        return redirect()->route('listings.show', [$listingUuid]);
    }

    /**
     * @param Request $request
     * @param         $listingUuid
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function show(Request $request, $listingUuid)
    {
        $listing = Listings::with(['items', 'items.listedItem'])
            ->where('uuid', $listingUuid)
            ->first();

        if (! $listing) {
            return $this->redirect()->to('/');
        }

        $rawCategories = collect(Listings::getStyleGroupings($listingUuid));

        $categories = $rawCategories->groupBy('style_name')->transform(function($item, $k){
            return $item->groupBy('size_name');
        });

        return $this->view('listings.show')->with(compact('categories', 'listing'));
    }
}
