<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Claim;

use Kabooodle\Bus\Events\Claim\NewGuestClaimEvent;
use Kabooodle\Libraries\Emails\PiperEmail;
use Kabooodle\Models\NotificationNotices;

/**
 * Class GuestClaimedEventHandler
 * @package Kabooodle\Bus\Handlers\Events\Claim
 */
class GuestClaimedEventHandler
{
    protected $mailer;

    /**
     * GuestClaimedEventHandler constructor.
     * @param PiperEmail $mailer
     */
    public function __construct(PiperEmail $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param NewGuestClaimEvent $event
     */
    public function handle(NewGuestClaimEvent $event)
    {
        $claim = $event->getClaim();
        $owner = $claim->listable->owner;
        $title = $claim->listable->getTitle().' was claimed by a guest, '.$claim->claimer->first_name.' '.$claim->claimer->last_name;

        $notification = new NotificationNotices;
        $notification->user_id = $owner->id;
        $notification->notification_id = null;
        $notification->reference_id = $claim->id;
        $notification->reference_type = get_class($claim);
        $notification->payload = '';
        $notification->title = $title;
        $notification->description = '';
        $notification->reference_url = route('claims.index');
        $notification->save();

        $this->mailer->sendClaimVerificationEmails($claim, $event->getEmail());
    }
}
