<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Profile;

use Kabooodle\Libraries\Emails\PiperEmail;
use Kabooodle\Bus\Events\Profile\UserWasSubscribedToPlanEvent;

/**
 * Class UserWasSubscribedToPlanEventHandler
 * @package Kabooodle\Bus\Handlers\Events\Profile
 */
class UserWasSubscribedToPlanEventHandler
{
    /**
     * @param UserWasSubscribedToPlanEvent $event
     */
    public function handle(UserWasSubscribedToPlanEvent $event)
    {
        $actor = $event->getActor();
        $subscription = $event->getSubscription();
        $plan = $event->getPlan();

        if ($actor->primaryEmail->isVerified()) {
            $mail = new PiperEmail;
            $mail->setView('profile.subscription.emails.subscribed')
                ->setParameters(['user' => $actor, 'subscription' => $subscription, 'plan' => $plan])
                ->setCallable(function ($m) use ($actor) {
                    $m->to($actor->email)->subject('Subscription activated on '.env('APP_NAME'));
                })
                ->send();
        }
    }
}
