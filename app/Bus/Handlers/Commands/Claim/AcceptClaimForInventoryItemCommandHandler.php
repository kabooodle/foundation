<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Claim;

use DB;
use Kabooodle\Models\Claims;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Claim\VerifyClaimCommand;
use Kabooodle\Bus\Events\Claim\ClaimWasAcceptedEvent;
use Kabooodle\Bus\Commands\Claim\AcceptClaimForInventoryItemCommand;
use Kabooodle\Foundation\Exceptions\Claim\RequestedQuantityCannotBeSatisfiedException;

/**
 * Class AcceptClaimForInventoryItemCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Claim
 */
class AcceptClaimForInventoryItemCommandHandler
{
    use DispatchesJobs;

    /**
     * @param AcceptClaimForInventoryItemCommand $command
     *
     * @return mixed
     * @throws RequestedQuantityCannotBeSatisfiedException
     */
    public function handle(AcceptClaimForInventoryItemCommand $command)
    {
        $claim = Claims::where('uuid', $command->getClaimId())->first();

        if (!$claim->isVerified()) {
            $this->dispatchNow(new VerifyClaimCommand($claim->token, $ignoreExpiredHolds = false));
        }

        return DB::transaction(function () use ($command, $claim) {
            $claim->accepted_price = $command->getAcceptedPrice() ? : null;
            $claim->accepted_on = $command->getTimestamp();
            $claim->accepted = true;

            $claim->save();

            event(new ClaimWasAcceptedEvent($command->getUser(), $claim));

            return $claim;
        });
    }
}
