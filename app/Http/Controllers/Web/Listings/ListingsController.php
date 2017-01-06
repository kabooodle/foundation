<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Listings;

use Illuminate\Http\Request;
use Kabooodle\Models\Listings;
use Kabooodle\Http\Controllers\Web\Controller;

/**
 * Class ListingItemsController
 */
class ListingsController extends Controller
{
    /**
     * @param Request $request
     * @param         $listingUuid
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function show(Request $request, $listingUuid)
    {
        $listing = Listings::where('uuid', $listingUuid)
            ->first();

        if (! $listing) {
            return $this->redirect()->to('/');
        }

        return $this->view('listings.show')->with(compact('listing'));
    }
}
