<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Listings;

use DB;
use Kabooodle\Bus\Commands\Listings\CreateListingItemCommand;
use Kabooodle\Bus\Events\Claim\NewGuestClaimEvent;
use Kabooodle\Models\Claims;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Events\Claim\NewItemWasClaimedEvent;
use Kabooodle\Bus\Commands\Claim\ClaimListedItemCommand;
use Kabooodle\Foundation\Exceptions\Claim\RequestedQuantityCannotBeSatisfiedException;
use Kabooodle\Models\ListingItems;

/**
 * Class CreateListingItemCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Claim
 */
class CreateListingItemCommandHandler
{
    use DispatchesJobs;

    /**
     * @param CreateListingItemCommand $command
     *
     * @return mixed
     * @throws RequestedQuantityCannotBeSatisfiedException
     */
    public function handle(CreateListingItemCommand $command)
    {
        // confirm quantity of 1 is still available for this particular item
        $quantityIsAvailable = $command->getItem()->canSatisfyRequestedQuantityOf(1);
        if (!$quantityIsAvailable) {
            throw new RequestedQuantityCannotBeSatisfiedException('Item no longer available due to insufficient quantity.');
        }

        $listing = DB::transaction(function () use ($command) {
            // Claim the item (put it into an escrow type account)
            $listing = ListingItems::create([
                'owner_id' => $command->getOwner()->id,
                'listable_id' => $command->getItem()->id,
                'type' => $command->getItem()->type,
                'status' => !$command->getItem()->status
            ]);

            return $listing;
        });

        return $listing;
    }
}