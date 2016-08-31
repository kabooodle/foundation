<?php

namespace Kabooodle\Bus\Handlers\Commands\Inventory;

use Kabooodle\Models\FlashsaleItems;
use Kabooodle\Bus\Commands\Inventory\DeleteInventoryFromSaleCommand;
use Kabooodle\Bus\Events\Inventory\InventoryItemWasRemovedFromSaleEvent;

/**
 * Class DeleteInventoryFromSaleCommand
 * @package Kabooodle\Bus\Handlers\Commands\Inventory
 */
class DeleteInventoryFromSaleCommandHandler
{
    /**
     * @param DeleteInventoryFromSaleCommand $command
     *
     * @return mixed
     */
    public function handle(DeleteInventoryFromSaleCommand $command)
    {
        $flashSaleItemId = $command->getflashSaleItemPivotId();

//        $flashSale = $command->getUser()->flashsaleItems->filter(function($item) use ($flashSaleItemId) {
//            return $item->pivot->id == $flashSaleItemId;
//        })->first();
//
//        $item->delete();


        $item = FlashsaleItems::where('seller_id', $command->getUser()->id)->where('id', $flashSaleItemId)->first();

        $item->forceDelete();

        event(new InventoryItemWasRemovedFromSaleEvent($command->getUser(), $item->inventory, $item->flashsale));

        return $item;
    }
}