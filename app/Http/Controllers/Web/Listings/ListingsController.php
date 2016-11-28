<?php

namespace Kabooodle\Http\Controllers\Web\Listings;

use Illuminate\Http\Request;
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
        $listings = user()->listings;

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

        return $this->view('listings.show')->with(compact('listing'));
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function detailed()
    {
        return $this->view('listings.detailed');
    }
}