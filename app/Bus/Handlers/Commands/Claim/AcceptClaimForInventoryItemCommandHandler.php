<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Claim;

use DB;
use Kabooodle\Models\Claims;
use Kabooodle\Bus\Events\Claim\ClaimWasAcceptedEvent;
use Kabooodle\Bus\Commands\Claim\AcceptClaimForInventoryItemCommand;

/**
 * Class AcceptClaimForInventoryItemCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Claim
 */
class AcceptClaimForInventoryItemCommandHandler
{
    /**
     * @param AcceptClaimForInventoryItemCommand $command
     *
     * @return mixed
     */
    public function handle(AcceptClaimForInventoryItemCommand $command)
    {
        return DB::transaction(function () use ($command) {
            $claim = Claims::where('uuid', $command->getClaimId())->first();
            $claim->accepted_price = $command->getAcceptedPrice() ? : null;
            $claim->accepted_on = $command->getTimestamp();
            $claim->accepted = true;

            $claim->save();

            event(new ClaimWasAcceptedEvent($command->getUser(), $claim));

            return $claim;
        });
    }
}
