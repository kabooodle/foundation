<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Profile;

use Kabooodle\Bus\Commands\Profile\SubscribeUserToPlanCommand;
use Kabooodle\Bus\Events\Profile\UserWasSubscribedToPlanEvent;
use Kabooodle\Foundation\Exceptions\Subscription\UserAlreadySubscribedToPlanException;
use Kabooodle\Foundation\Exceptions\Subscription\UserHasNoCreditCardOnFileException;
use Kabooodle\Models\User;
use Laravel\Cashier\Subscription;
use Stripe\Error\InvalidRequest;

/**
 * Class SubscribeUserToPlanCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Profile
 */
class SubscribeUserToPlanCommandHandler
{
    /**
     * @param SubscribeUserToPlanCommand $command
     *
     * @return Subscription|null
     *
     * @throws InvalidRequest
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
            if (!$actor->getCard()) {
                throw new UserHasNoCreditCardOnFileException;
            }

            // Create their first ever subscription to the plan!
            // Woot woot! congrats!
            $poppingCherry = true;
            $subscription = $this->newSubscription(
                $actor,
                $subscriptionName,
                $plan,
                (int) ($skipTrial ? 0 : $trialDays)
            );
        } else {
            // Has the user already subscribed to the plan?
            if ($actor->subscribedToPlan($plan, $subscriptionName)) {
                throw new UserAlreadySubscribedToPlanException($plan);
            }

            // At this point, the user is clearly subscribed to SOME SORT OF plan
            // lets swap them over to the newly requested plan.
            $swapping = true;

            $subscription = $actor->subscription($subscriptionName);
            if ($skipTrial) {
                $subscription->trial_ends_at = null;
            }

            if ($subscription->cancelled() && $subscription->onGracePeriod()) {
                $subscription->resume();
            } else {
                try {
                    $subscription->swap($plan);
                } catch (InvalidRequest $e) {
                    // Subscription no longer exists so create a new one.
                    if ($e->getHttpStatus() == 404) {
                        $subscription = $this->newSubscription($actor, $subscriptionName, $plan, 0);
                    } else {
                        // Unsure of other expcetions that can be thrown.
                        throw $e;
                    }
                }
            }
        }

        event(new UserWasSubscribedToPlanEvent($actor, $subscription, $plan, $poppingCherry, $swapping));

        return $subscription;
    }

    /**
     * @param User $actor
     * @param      $subscriptionName
     * @param      $plan
     * @param int  $trialDays
     *
     * @return Subscription
     */
    protected function newSubscription(User $actor, $subscriptionName, $plan, $trialDays = 0)
    {
        return $actor->newSubscription($subscriptionName, $plan)
            ->trialDays((int) $trialDays)
            ->create(null, [
                'email' => $actor->email,
                'id' => $actor->id,
            ]);
    }
}