<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
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
     * @var string
     */
    public $subject;

    /**
     * @param ClaimWasAcceptedEvent $event
     */
    public function handle(ClaimWasAcceptedEvent $event)
    {
        $claim = $event->getClaim();
        $claimedBy = $claim->claimer;
        $acceptedBy = $event->getActor();

        $this->subject = 'Your claim on '.$claim->listable->getTitle().' - '.currency($claim->price).', was accepted by '. $claim->listable->username;

        if ($claimedBy->primaryEmail && $claimedBy->primaryEmail->isVerified()) {
            $this->toEmail($claim, $claimedBy);
        }

        $this->toDatabase($claim, $claimedBy);
    }

    public function toEmail(Claims $claim, User $claimedBy)
    {
        $subject = $this->subject;
        $mail = new PiperEmail;
        $mail->setView('claims.emails.accepted_toclaimer')
            ->setParameters(['item' => $claim->listedItem, 'claim' => $claim])
            ->setCallable(function ($mail) use ($claimedBy, $subject) {
                $mail->to($claimedBy->email)->subject($subject);
            })
            ->send();
    }

    /**
     * @param Claims $claim
     * @param User   $claimedBy
     */
    public function toDatabase(Claims $claim, User $claimedBy)
    {
        $title = $this->subject;

        $notification = new NotificationNotices;
        $notification->user_id = $claimedBy->id;
        $notification->notification_id = null;
        $notification->reference_id = $claim->id;
        $notification->reference_type = get_class($claim);
        $notification->payload = '';
        $notification->title = $title;
        $notification->description = '';
        $notification->reference_url = route('profile.claims.show', [$claim->getUUID()]);
        $notification->save();
    }
}