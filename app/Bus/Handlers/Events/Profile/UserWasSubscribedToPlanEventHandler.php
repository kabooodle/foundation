<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Profile;

use Illuminate\Contracts\Mail\Mailer;
use Kabooodle\Bus\Events\Profile\UserWasSubscribedToPlanEvent;

/**
 * Class UserWasSubscribedToPlanEventHandler
 * @package Kabooodle\Bus\Handlers\Events\Profile
 */
class UserWasSubscribedToPlanEventHandler
{
    /**
     * UserWasSubscribedToPlanEventHandler constructor.
     *
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param UserWasSubscribedToPlanEvent $event
     */
    public function handle(UserWasSubscribedToPlanEvent $event)
    {
        $actor = $event->getActor();
        $subscription = $event->getSubscription();
        $plan = $event->getPlan();

        $this->mailer->queue('profile.subscription.emails.subscribed', ['user' => $actor, 'subscription' => $subscription, 'plan' => $plan], function ($m) use ($actor) {
            $m->to($actor->email)->subject('Subscription activated on '.env('APP_NAME'));
        });
    }
}