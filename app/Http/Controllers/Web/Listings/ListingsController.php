<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Listings;

use Illuminate\Http\Request;
use Kabooodle\Models\Listings;
use Kabooodle\Models\ListingItems;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Kabooodle\Http\Controllers\Traits\PaginatesTrait;

/**
 * Class ListingItemsController
 */
class ListingsController extends Controller
{
    use ObfuscatesIdTrait, PaginatesTrait;

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
     * @param string  $listingName
     * @param string  $listingItemUuid
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function customLink(Request $request, $listingName, $listingItemUuid)
    {
        $inventoryId = $this->obfuscateFromURIString($listingItemUuid);
        $listingItem = ListingItems::where('listable_id', '=', $inventoryId)->with('listing', 'listedItem')
            ->whereHas('listing', function($q) use ($listingName) {
                $q->where('name', '=', $listingName);
            })->firstOrFail();

        return $this->view('listings.items.show')->with(compact('listingItem'));
    }

    /**
     * @param Request $request
     * @param         $listingUuid
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function show(Request $request, $listingUuid)
    {
        $listing = Listings::with(['items.listedItem'])
            ->where('uuid', $listingUuid)
            ->first();

        if (! $listing) {
            return $this->redirect()->to('/');
        }

        $rawCategories = collect(Listings::getStyleGroupings($listingUuid));

        $categories = $rawCategories->sortBy('style_name')->groupBy('style_name')->transform(function($item, $k){
            return $item->groupBy('size_name');
        });

        return $this->view('listings.show')->with(compact('categories', 'listing'));
    }
}
