<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Listings;

use Illuminate\Http\Request;
use Kabooodle\Models\ListingItems;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;

/**
 * Class ListingItemsController
 */
class ListingItemsController extends Controller
{
    use ObfuscatesIdTrait;

    /**
     * @param Request $request
     * @param string  $listingItemsHash
     *
     * @return $this
     */
    public function show(Request $request, $listingItemsHash)
    {
        $listingItemsId = $this->obfuscateStringToId($listingItemsHash);
        $listingItem = ListingItems::find($listingItemsId);

        if (! $listingItem) {
            return $this->redirect()->to('/');
        }

        return $this->view('listings.items.show')->with(compact('listingItem'));
    }
}
