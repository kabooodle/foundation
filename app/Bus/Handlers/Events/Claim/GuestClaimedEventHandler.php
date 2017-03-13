<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Claim;

use Kabooodle\Bus\Events\Claim\NewGuestClaimEvent;
use Kabooodle\Libraries\Emails\PiperEmail;

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
        $this->mailer->sendClaimVerificationEmails($event->getClaim(), $event->getEmail());
    }
}
