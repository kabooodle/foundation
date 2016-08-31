<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Inventory;

use Binput;
use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Bus\Commands\Inventory\AddInventoryToSalesCommand;

/**
 * Class InventoryApiController
 * @package Kabooodle\Http\Controllers\Api\Inventory
 */
class InventoryApiController extends AbstractApiController
{
    /**
     * @param Request $request
     *
     * @return string
     */
    public function associate(Request $request)
    {
        $flashsaleIds = Binput::get('flashsales', []);
        $inventoryIds = Binput::get('inventoryids', []);

        $result = $this->dispatchNow(new AddInventoryToSalesCommand($this->getUser(), $inventoryIds, $flashsaleIds));

        return $result;
    }
}