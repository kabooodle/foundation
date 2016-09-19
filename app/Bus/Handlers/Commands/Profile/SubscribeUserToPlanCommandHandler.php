<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Profile;

use Kabooodle\Models\User;
use Laravel\Cashier\Subscription;
use Kabooodle\Bus\Commands\Profile\SubscribeUserToPlanCommand;
use Kabooodle\Bus\Events\Profile\UserWasSubscribedToPlanEvent;
use Kabooodle\Foundation\Exceptions\Subscription\UserHasNoCreditCardOnFileException;
use Kabooodle\Foundation\Exceptions\Subscription\UserAlreadySubscribedToPlanException;

/**
 * Class SubscribeUserToPlanCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Profile
 */
class SubscribeUserToPlanCommandHandler
{
    /**
     * @param SubscribeUserToPlanCommand $command
     *
     * @return Subscription
     * @throws UserAlreadySubscribedToPlanException
     * @throws UserHasNoCreditCardOnFileException
     */
    public function handle(SubscribeUserToPlanCommand $command)
    {
        /** @var User $actor */
        $actor = $command->getActor();
        $plan = $command->getPlanId();
        $skipTrial = $command->skipTrial();
        $trialDays = $command->getTrialDays();
        $subscriptionName = $command->getSubscriptionName();

        $poppingCherry = false;
        $swapping = false;

        // Does the user have any subscriptions at all?
        if ($actor->subscriptions()->count() == 0) {

            // No card?!
            if (! $actor->getCard()) {
                throw new UserHasNoCreditCardOnFileException;
            }

            // Create their first ever subscription to the plan!
            // Woot woot! congrats!
            $poppingCherry = true;
            $subscription = $actor->newSubscription($subscriptionName, $plan)
                ->trialDays((int) ($skipTrial ? 0 : $trialDays))
                ->create(null, [
                    'email' => $actor->email,
                    'id' => $actor->id,
                ]);
        } else {
            // Has the user already subscribed to the plan?
            if ($actor->subscribedToPlan($plan, $subscriptionName)) {
                 throw new UserAlreadySubscribedToPlanException($plan);
            }

            // At this point, the user is clearly subscribed to SOME SORT OF plan
            // lets swap them over to the newly requested plan.
            $swapping = true;
            $subscription = $actor->subscription($subscriptionName)
                ->skipTrial()
                ->swap($plan);
        }

        event(new UserWasSubscribedToPlanEvent($actor, $subscription, $plan, $poppingCherry, $swapping));

        return $subscription;
    }
}