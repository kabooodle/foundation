<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Claim;

use Kabooodle\Models\User;
use Kabooodle\Models\Claims;
use Illuminate\Queue\InteractsWithQueue;
use Kabooodle\Models\NotificationNotices;
use Kabooodle\Libraries\Emails\PiperEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kabooodle\Bus\Events\Claim\ClaimWasAcceptedEvent;

/**
 * Class NotifyClaimWasAccepted
 */
class NotifyClaimWasAccepted implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @param ClaimWasAcceptedEvent $event
     */
    public function handle(ClaimWasAcceptedEvent $event)
    {
        $claim = $event->getClaim();
        $claimedBy = $event->getActor();

        if ($claimedBy->primaryEmail && $claimedBy->primaryEmail->isVerified()) {
            $this->toEmail($claim, $claimedBy);
        }

        $this->toDatabase($claim, $claimedBy);
    }

    public function toEmail(Claims $claim, User $claimedBy)
    {
        $mail = new PiperEmail;
        $mail->setView('inventory.claims.emails.accepted_toclaimer')
            ->setParameters(['item' => $claim->inventoryItem, 'claim' => $claim])
            ->setCallable(function ($mail) use ($claimedBy) {
                $mail->to($claimedBy->email)->subject('Item claim accepted.');
            })
            ->send();
    }

    /**
     * @param Claims $claim
     * @param User   $claimedBy
     */
    public function toDatabase(Claims $claim, User $claimedBy)
    {
        $title = 'Your claim on '.$claim->inventoryItem->getNameAndSize().' - $'.$claim->price.', was accepted by '. $claim->inventoryItem->owner->username;

        $notification = new NotificationNotices;
        $notification->user_id = $claimedBy->id;
        $notification->notification_id = null;
        $notification->reference_id = $claim->id;
        $notification->reference_type = get_class($claim);
        $notification->payload = '';
        $notification->title = $title;
        $notification->description = '';
        $notification->reference_url = route('profile.purchases.index');
        $notification->save();
    }
}