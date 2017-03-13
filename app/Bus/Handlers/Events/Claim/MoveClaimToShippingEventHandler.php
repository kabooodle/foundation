<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Claim;

use Kabooodle\Models\ShippingQueue;
use Kabooodle\Bus\Events\Claim\ClaimWasAcceptedEvent;

/**
 * Class MoveClaimToShippingEventHandler
 * @package Kabooodle\Bus\Handlers\Events\Claim
 */
class MoveClaimToShippingEventHandler
{
    /**
     * @param ClaimWasAcceptedEvent $event
     * @return bool
     */
    public function handle(ClaimWasAcceptedEvent $event)
    {
        $user = $event->getActor();
        $claim = $event->getClaim();

        if ($user->usesKabooodleAsShipper()) {
            ShippingQueue::create([
                'user_id' => $user->id,
                'claim_id' => $claim->id
            ]);
        }

        return true;
    }
}
