<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Claim;

use Kabooodle\Models\NotificationNotices;
use Kabooodle\Models\User;
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
        $claimedBy = $claim->claimedBy;

        if ($claimedBy->primaryEmail->isVerified()) {
            $this->toEmail($claim, $claimedBy);
        }

        $this->toDatabase($claim, $claimedBy);
    }

    /**
     * @param Claims  $claim
     * @param User    $claimedBy
     */
    public function toEmail(Claims $claim, User $claimedBy)
    {
        $mail = new PiperEmail;
        $mail->setView('inventory.claims.emails.rejected_toclaimer')
            ->setParameters(['item' => $claim->listedItem, 'claim' => $claim])
            ->setCallable(function ($mail) use ($claimedBy) {
                $mail->to($claimedBy->email)->subject('Item claim rejected.');
            })
            ->send();
    }

    /**
     * @param Claims $claim
     * @param User   $claimedBy
     */
    public function toDatabase(Claims $claim, User $claimedBy)
    {
        $title = 'Your claim on '.$claim->listedItem->getNameAndSize().' was rejected by '. $claim->rejector->full_name;

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
