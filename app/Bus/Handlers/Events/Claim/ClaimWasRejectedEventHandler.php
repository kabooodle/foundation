<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
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
     * @var string
     */
    public $subject;

    /**
     * @param ClaimWasRejectedEvent $event
     */
    public function handle(ClaimWasRejectedEvent $event)
    {
        /** @var Claims $claim */
        $claim = $event->getClaim();
        $claimedBy = $claim->claimer;

        $this->subject = 'Your claim on '.$claim->listable->getTitle().' was rejected by '. $claim->rejector->full_name;

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
        $subject = $this->subject;
        $mail = new PiperEmail;
        $mail->setView('claims.emails.rejected_toclaimer')
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
