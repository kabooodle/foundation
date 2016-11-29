<?php

namespace Kabooodle\Http\Controllers\Web\Listings;

use Illuminate\Http\Request;
use Kabooodle\Models\Listings;
use Kabooodle\Http\Controllers\Web\Controller;

/**
 * Class ListingsController
 */
class ListingsController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $listings = Listings::getQueriedListings(user()->id);

        return $this->view('listings.index')->with(compact('listings'));
    }

    /**
     * @param Request $request
     * @param $uuid
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Request $request, $uuid)
    {
        $listing = user()->listings->where('uuid', $uuid)->first();
        if(!$listing) {
            return redirect()->to(route('listings.index'));
        }
        $listings = $listing->listingsGroupedByItemTypeGrouping(user()->id);

        return $this->view('listings.show')->with(compact('listing', 'listings'));
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
        $listing = Listings::where('uuid', $uuid)
            ->where('owner_id', user()->id)
            ->where('type', $type)
            ->whereHas('listingItems', function($q) use ($type, $id) {
                if($type == 'facebook') {
                    $q->where('fb_album_node_id', $id);
                } else {
                    $q->where('flashsale_id', $id);
                }
            })
            ->first();
        if(!$listing) {
            return redirect()->to(route('listings.index'));
        }

        return $this->view('listings.detailed')->with(compact('listing'));
    }
}