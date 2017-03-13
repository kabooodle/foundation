<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Claim;

use Kabooodle\Bus\Commands\Claim\VerifyClaimCommand;
use Kabooodle\Bus\Events\Claim\NewItemWasClaimedEvent;
use Kabooodle\Foundation\Exceptions\Claim\ClaimRejectedException;
use Kabooodle\Foundation\Exceptions\Claim\RequestedQuantityCannotBeSatisfiedException;
use Kabooodle\Models\Claims;

/**
 * Class VerifyClaimCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\User
 */
class VerifyClaimCommandHandler
{
    protected $claims;

    /**
     * VerifyClaimCommandHandler constructor.
     * @param Claims $claims
     */
    public function __construct(Claims $claims)
    {
        $this->claims = $claims;
    }

    /**
     * @param VerifyClaimCommand $command
     * @return mixed
     * @throws ClaimRejectedException
     * @throws RequestedQuantityCannotBeSatisfiedException
     */
    public function handle(VerifyClaimCommand $command)
    {
        $claim = $this->claims->whereVerified(0)->whereToken($command->getToken())->firstOrFail();

        if ($claim->isRejected()) {
            throw new ClaimRejectedException('Your claim has been rejected by the seller.');
        }
        // confirm quantity of 1 is still available for this particular item
        $quantityIsAvailable = $claim->listedItem->canSatisfyRequestedQuantityOf(1);
        if (!$quantityIsAvailable) {
            throw new RequestedQuantityCannotBeSatisfiedException('Item no longer available due to insufficient quantity.');
        }
        $claim->verify();

        if ($claim->isVerified()) {
            $claim->listedItem->decrement('initial_qty');

            event(new NewItemWasClaimedEvent($claim));
        }
        return $claim;
    }
}
