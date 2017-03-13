<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Claim;

use Carbon\Carbon;
use DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Claim\VerifyClaimCommand;
use Kabooodle\Models\Claims;
use Kabooodle\Bus\Events\Claim\ClaimWasAcceptedEvent;
use Kabooodle\Bus\Commands\Claim\AcceptClaimForClaimableItemCommand;

/**
 * Class AcceptClaimForClaimableItemCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Claim
 */
class AcceptClaimForClaimableItemCommandHandler
{
    use DispatchesJobs;

    /**
     * @param AcceptClaimForClaimableItemCommand $command
     *
     * @return mixed
     */
    public function handle(AcceptClaimForClaimableItemCommand $command)
    {
        $claim = Claims::where('uuid', $command->getClaimId())->first();
        if (!$claim->isVerified()) {
            $this->dispatchNow(new VerifyClaimCommand($claim->token));
        }
        return DB::transaction(function () use ($command, $claim) {
            $claim->accepted_price = $command->getAcceptedPrice() ? : null;
            $claim->accepted_on = $command->getTimestamp();
            $claim->accepted = true;

            // If user's subscription is NOT merchant plus, then
            // flag the claim as having been manually shipped.
            if (! $command->getUser()->isSubscribedToMerchantPlus()) {
                $claim->shipped_manually = 1;
                $claim->shipped_manually_on = Carbon::now();
            }

            $claim->save();

            event(new ClaimWasAcceptedEvent($command->getUser(), $claim));

            return $claim;
        });
    }
}
