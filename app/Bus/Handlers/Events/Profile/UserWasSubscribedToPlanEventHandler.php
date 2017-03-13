<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Profile;

use Kabooodle\Models\User;
use Laravel\Cashier\Subscription;
use Kabooodle\Libraries\Emails\PiperEmail;
use Kabooodle\Bus\Events\User\ReferralHasQualified;
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
            $this->toEmail($actor, $subscription, $plan);
        }

        // Is this really their first subscription ever
        if ($this->checkUsersFirstSubscriptionEver($event)) {

            //
            if ($referrer = $actor->referredBy) {
                event(new ReferralHasQualified($actor, $referrer, $subscription, $plan));
            }
        }
    }

    /**
     * @param User $actor
     * @param Subscription $subscription
     * @param string $plan
     */
    public function toEmail(User $actor, Subscription $subscription, string $plan)
    {
        $mail = new PiperEmail;
        $mail->setView('profile.subscription.emails.subscribed')
            ->setParameters(['user' => $actor, 'subscription' => $subscription, 'plan' => $plan])
            ->setCallable(function ($m) use ($actor) {
                $m->to($actor->email)->subject('Subscription activated on '.env('APP_NAME'));
            })
            ->send();
    }

    /**
     * @param UserWasSubscribedToPlanEvent $event
     * @return bool
     */
    public function checkUsersFirstSubscriptionEver(UserWasSubscribedToPlanEvent $event)
    {
        /** @var User $actor */
        $actor = $event->getActor()->fresh();

        /** @var bool $eventSaidItWasFirstSubscription */
        $eventSaidItWasFirstSubscription = $event->isPoppingCherry();

        return $actor->subscriptions->count() == 1 && $eventSaidItWasFirstSubscription == true;
    }
}
