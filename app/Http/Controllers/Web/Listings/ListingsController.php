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
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function show(Request $request, $listingUuid)
    {
        $listing = Listings::with(['items', 'items.inventoryItem'])
            ->where('uuid', $listingUuid)
            ->first();

        if (! $listing) {
            return $this->redirect()->to('/');
        }

        $items = $listing->items->sortBy(function($item){
            return $item->inventoryItem->style->name;
        });

        $items = $this->paginateData($request, $items);

        $categories = Listings::getStyleGroupings($listingUuid);

        return $this->view('listings.show')->with(compact('categories', 'items', 'listing'));
    }
}
