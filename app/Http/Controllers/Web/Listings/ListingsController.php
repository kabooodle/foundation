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
        return $this->view('listings.index');
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function show()
    {
        return $this->view('listings.show');
    }
}