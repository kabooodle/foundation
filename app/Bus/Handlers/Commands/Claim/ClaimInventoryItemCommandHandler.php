<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Claim;

use DB;
use Kabooodle\Models\Claims;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Events\Claim\NewItemWasClaimedEvent;
use Kabooodle\Bus\Commands\Claim\ClaimInventoryItemCommand;
use Kabooodle\Foundation\Exceptions\Claim\RequestedQuantityCannotBeSatisfiedException;

/**
 * Class ClaimInventoryItemCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Claim
 */
class ClaimInventoryItemCommandHandler
{
    use DispatchesJobs;

    /**
     * @param ClaimInventoryItemCommand $command
     *
     * @return bool|mixed
     * @throws RequestedQuantityCannotBeSatisfiedException
     */
    public function handle(ClaimInventoryItemCommand $command)
    {
        // confirm quantity of 1 is still available for this particular item
        $quantityIsAvailable = $command->getInventoryItem()->canSatisfyRequestedQuantityOf(1);
        if (!$quantityIsAvailable) {
            throw new RequestedQuantityCannotBeSatisfiedException('Item no longer available due to insufficient quantity.');
        }

        $claim = DB::transaction(function () use ($command) {
            // Claim the item (put it into an escrow type account)
            $claim = Claims::create([
                'inventory_id' => $command->getInventoryItem()->id,
                'claimed_by' => $command->getClaimedBy()->id,
                'inventory_item_object_data' => $command->getInventoryItem(),
                'price' => $command->getInventoryItem()->getPrice(),
                'shoppable_id' => $command->getShoppable()->id,
                'shoppable_type' => get_class($command->getShoppable())
            ]);

            // Decrement the inventory item's quantity
            $command->getInventoryItem()->decrement('initial_qty');

            return $claim;
        });

        // Fire event
        event(new NewItemWasClaimedEvent($claim));

        return $claim;
    }
}