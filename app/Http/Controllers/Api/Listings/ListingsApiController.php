<?php

namespace Kabooodle\Http\Controllers\Api\Listings;

use Binput;
use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Api\AbstractApiController;

/**
 * Class ListingsApiController
 */
class ListingsApiController extends AbstractApiController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $listings = $this->getUser()->listings;

        return $this->setData($listings)->respond();
    }
}