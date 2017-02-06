<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Claim;

use Kabooodle\Models\Claims;
use Kabooodle\Bus\Commands\Claim\VerifyClaimCommand;
use Kabooodle\Bus\Events\Claim\NewItemWasClaimedEvent;
use Kabooodle\Foundation\Exceptions\Claim\ClaimRejectedException;
use Kabooodle\Foundation\Exceptions\Claim\ClaimVerificationExpiredException;
use Kabooodle\Foundation\Exceptions\Claim\RequestedQuantityCannotBeSatisfiedException;

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
     * @throws ClaimVerificationExpiredException
     * @throws RequestedQuantityCannotBeSatisfiedException
     */
    public function handle(VerifyClaimCommand $command)
    {
        /** @var Claims $claim */
        $claim = $this->claims->whereVerified(0)->whereToken($command->getToken())->firstOrFail();

        if ($claim->isRejected()) {
            throw new ClaimRejectedException('Your claim has been rejected by the seller.');
        }

        if ($claim->claimVerificationExpired() && ! $command->shouldIgnoreExpireHolds()) {
            throw new ClaimVerificationExpiredException;
        }

        // confirm quantity of 1 is still available for this particular item
        $quantityIsAvailable = $claim->inventoryItem->canSatisfyRequestedQuantityOfExcludingOnHolds(1, [$claim->id]);
        if (!$quantityIsAvailable) {
            throw new RequestedQuantityCannotBeSatisfiedException('Item no longer available due to insufficient quantity.');
        }
        $claim->verify();

        if ($claim->isVerified()) {
            $claim->inventoryItem->decrement('initial_qty');

            event(new NewItemWasClaimedEvent($claim));
        }
        return $claim;
    }
}
