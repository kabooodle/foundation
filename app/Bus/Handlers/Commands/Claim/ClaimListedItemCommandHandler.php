<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Claim;

use DB;
use Kabooodle\Bus\Commands\Claim\AcceptClaimForClaimableItemCommand;
use Kabooodle\Bus\Events\Claim\NewGuestClaimEvent;
use Kabooodle\Models\Claims;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Events\Claim\NewItemWasClaimedEvent;
use Kabooodle\Bus\Commands\Claim\ClaimListedItemCommand;
use Kabooodle\Foundation\Exceptions\Claim\RequestedQuantityCannotBeSatisfiedException;

/**
 * Class ClaimListedItemCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Claim
 */
class ClaimListedItemCommandHandler
{
    use DispatchesJobs;

    /**
     * @param ClaimListedItemCommand $command
     *
     * @return bool|mixed
     * @throws RequestedQuantityCannotBeSatisfiedException
     */
    public function handle(ClaimListedItemCommand $command)
    {
        // confirm quantity of 1 is still available for this particular item
        $quantityIsAvailable = $command->getListedItem()->canSatisfyRequestedQuantityOf(1);
        if (!$quantityIsAvailable) {
            throw new RequestedQuantityCannotBeSatisfiedException('Item no longer available due to insufficient quantity.');
        }

        $claim = DB::transaction(function () use ($command) {
            // Claim the item (put it into an escrow type account)
            $claim = Claims::create([
                'listable_type' => get_class($command->getListedItem()),
                'listable_id' => $command->getListedItem()->id,
                'claimed_by' => $command->getClaimedBy()->id,
                'listable_item_object_data' => $command->getListedItem(),
                'verified' => !$command->isGuest(),
                'price' => $command->getListedItem()->getPrice(),
                'listing_item_id' => $command->getListingItem()->id,
            ]);

            // Decrement the inventory item's quantity
            if ($claim->isVerified()) {
                $command->getListedItem()->decrementInitialQty(1);
                $this->dispatchNow(new AcceptClaimForClaimableItemCommand($claim->owner, $claim->uuid, $claim->price));
            }

            return $claim;
        });

        // Fire event
        if ($claim->isVerified()) {
            event(new NewItemWasClaimedEvent($claim));
        } else {
            event(new NewGuestClaimEvent($claim, $command->getEmail()));
        }

        return $claim;
    }
}
