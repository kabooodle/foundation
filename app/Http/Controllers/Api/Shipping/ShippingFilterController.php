<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Shipping;

use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Api\AbstractApiController;

/**
 * Class ShippingFilterController
 * @package Kabooodle\Http\Controllers\Api\Shipping
 */
class ShippingFilterController extends AbstractApiController
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        return $this->setData($this->filterRecipients([]))->respond();
    }

    /**
     * @param string $query
     *
     * @return mixed
     */
    public function filterRecipients($query)
    {
        return $this->user()->shippingTransactions()->first()->shipment->claimer;
    }
}