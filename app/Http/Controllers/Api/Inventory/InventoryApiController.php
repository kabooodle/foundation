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
use Kabooodle\Bus\Commands\Inventory\DeleteInventoryFromSaleCommand;

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
        $flashsaleIds = Binput::get('flashsalesales_ids', []);
        $facebookAlbumIds = Binput::get('fb_albums', []);
        $inventoryIds = Binput::get('selected_items_ids', []);

        $result = $this->dispatchNow(new AddInventoryToSalesCommand($this->getUser(), $inventoryIds, $flashsaleIds, $facebookAlbumIds));

        return $this->setData($result)->respond();
    }

    /**
     * @param Request $request
     * @param         $username
     * @param         $flashSaleItemId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyAssociation(Request $request, $username, $flashSaleItemId)
    {
        $this->dispatchNow(new DeleteInventoryFromSaleCommand($this->getUser(), $flashSaleItemId));

        return $this->noContent();
    }
}