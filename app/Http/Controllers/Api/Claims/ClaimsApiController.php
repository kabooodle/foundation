<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Claims;

use Kabooodle\Http\Controllers\Api\AbstractApiController;

/**
 * Class ClaimsApiController
 * @package Kabooodle\Http\Controllers\Api\Claims
 */
class ClaimsApiController extends AbstractApiController
{
    /**
     * @return \Dingo\Api\Http\Response\Factory
     */
    public function index()
    {
        $data = $this->getUser()->claimsAsSellerNoShipping();

        return $this->collection($data);
    }
}