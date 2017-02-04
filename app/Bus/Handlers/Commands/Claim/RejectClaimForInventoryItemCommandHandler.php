<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Claim;

use DB;
use Carbon\Carbon;
use Kabooodle\Models\Claims;
use Kabooodle\Bus\Events\Claim\ClaimWasRejectedEvent;
use Kabooodle\Bus\Commands\Claim\RejectClaimForInventoryItemCommand;

/**
 * Class RejectClaimForInventoryItemCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Claim\
 */
class RejectClaimForInventoryItemCommandHandler
{
    /**
     * @param RejectClaimForInventoryItemCommand $command
     *
     * @return mixed|Claims
     */
    public function handle(RejectClaimForInventoryItemCommand $command)
    {
        return DB::transaction(function () use ($command) {
            $claim = Claims::where('uuid', $command->getClaimId())->first();
            $claim->rejected_by = user()->id;
            $claim->rejected_on = Carbon::now();
            $claim->rejected_reason = $command->getNotes();
            $claim->accepted = false;
            $claim->save();

            // Increment doesn't trigger any eloquent events.
            $currentQty = $claim->inventoryItem->initial_qty;
            $claim->inventoryItem->initial_qty = $currentQty+1;
            $claim->inventoryItem->save();

            event(new ClaimWasRejectedEvent($command->getUser(), $claim));

            return $claim;
        });
    }
}
