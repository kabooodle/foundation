<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Inventory;

use Kabooodle\Models\User;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Inventory\AddInventoryToSalesCommand;
use Kabooodle\Bus\Events\Inventory\InventoryItemWasAddedToSaleEvent;

/**
 * Class AddInventoryToSalesCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Inventory
 */
class AddInventoryToSalesCommandHandler
{
    use DispatchesJobs;

    /**
     * TODO: Abstract this so that its better tested.
     *
     * @param AddInventoryToSalesCommand $command
     *
     * @return mixed|null
     */
    public function handle(AddInventoryToSalesCommand $command)
    {
        $user = $command->getUser();
        $flashSaleIds = $command->getFlashSalesIds();
        $inventoryIds = $command->getInventoryIds();

        // Lazy load the relationship we will be poking at.
        $user->load('flashsaleItems');

        // Determine if we're also adding the item to the user's own store.
        $addedToOwnStore = false;
        if (in_array($user->username, $flashSaleIds)) {
            $addedToOwnStore = true;
            unset($flashSaleIds[array_search($user->username, $flashSaleIds)]);
        }

        // Make sure we still have an array to associate anything to
        if (count($flashSaleIds) > 0 ) {

            foreach ($inventoryIds as $inventoryId) {
                foreach ($flashSaleIds as $flashSaleId) {
                    // Make sure we dont add an item to a flashsale that is already there.
                    if (! $this->itemAlreadyInSale($user, $flashSaleId, $inventoryId)) {

                        // Perform insertion
                        $user->flashsaleItems()->attach($flashSaleId, ['inventory_id' => $inventoryId] );

                        // Fire event
                        event(new InventoryItemWasAddedToSaleEvent($user, $flashSaleId, $inventoryId));
                    }
                }
            }

            // refresh the relationship
            $user->load('flashsaleItems');

            return $user->flashsaleItems;
        }

        return null;
    }

    /**
     * @param User $user
     * @param      $flashsaleId
     * @param      $inventoryId
     *
     * @return mixed
     */
    protected function itemAlreadyInSale(User $user, $flashsaleId, $inventoryId)
    {
        $user->load('flashsaleItems');

        return $user->flashsaleItems->filter(function($item) use ($flashsaleId, $inventoryId) {
            return $item->id == $flashsaleId && $item->pivot->inventory_id == $inventoryId;
        })->first();
    }
}