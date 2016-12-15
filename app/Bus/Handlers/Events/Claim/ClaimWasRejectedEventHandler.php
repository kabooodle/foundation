<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Claim;

use Kabooodle\Models\Claims;
use Illuminate\Queue\InteractsWithQueue;
use Kabooodle\Libraries\Emails\PiperEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kabooodle\Bus\Events\Claim\ClaimWasRejectedEvent;

/**
 * Class ClaimWasRejectedEvent
 * @package Kabooodle\Bus\Events
 */
class ClaimWasRejectedEventHandler implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @param ClaimWasRejectedEvent $event
     */
    public function handle(ClaimWasRejectedEvent $event)
    {
        /** @var Claims $claim */
        $claim = $event->getClaim();
        $rejectedBy = $event->getActor();
        $claimedBy = $claim->claimedBy;

        if ($claimedBy->primaryEmail->isVerified()) {
            $mail = new PiperEmail;
            $mail->setView('inventory.claims.emails.rejected_toclaimer')
                ->setParameters(['item' => $claim->inventoryItem, 'claim' => $claim])
                ->setCallable(function ($mail) use ($claimedBy) {
                    $mail->to($claimedBy->email)->subject('Item claim rejected.');
                })
                ->send();
        }
    }
}
