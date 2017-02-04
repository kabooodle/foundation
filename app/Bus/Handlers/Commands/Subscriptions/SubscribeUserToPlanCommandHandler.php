<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Subscriptions;

use Exception;
use Kabooodle\Models\User;
use Stripe\Error\InvalidRequest;
use Laravel\Cashier\Subscription;
use Kabooodle\Bus\Events\Profile\UserWasSubscribedToPlanEvent;
use Kabooodle\Bus\Commands\Subscriptions\SubscribeUserToPlanCommand;
use Kabooodle\Foundation\Exceptions\Subscription\UserHasNoCreditCardOnFileException;
use Kabooodle\Foundation\Exceptions\Subscription\UserAlreadySubscribedToPlanException;

/**
 * Class SubscribeUserToPlanCommandHandler
 */
class SubscribeUserToPlanCommandHandler
{
    /**
     * @param SubscribeUserToPlanCommand $command
     * @return Subscription|null
     *
     * @throws Exception
     * @throws InvalidRequest
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

        // No card?!
        if (!$actor->getCard()) {
            throw new UserHasNoCreditCardOnFileException;
        }

        // Stores whether or not this is the first subscription ever for the user.
        $poppingCherry = false;

        // Stores whether we are swapping the users' plan with a different one.
        $swapping = false;

        try {
            // Does the user have any subscriptions at all?
            if ($actor->subscriptions()->count() == 0) {

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

                // At this point, the user is clearly subscribed to SOME SORT OF plan
                // We need to determine if the current plan they have has been cancelled but is on grace period
                // If so, we will just resume their subscription.
                // Otherwise, we will swap their existing with the new subscription.
                $subscription = $actor->currentSubscription();
                if ($skipTrial) {
                    $subscription->trial_ends_at = null;
                }

                $subscription->name = $subscriptionName;

                // If the current subscription has been cancelled or is on the grace period,
                // then we are going to resume it.
                if ($subscription->cancelled() && $subscription->onGracePeriod()) {
                    $subscription->resume();
                    $subscription->ends_at = null;
                } else {
                    // We can't swap to the same plan.
                    if ($actor->subscribedToPlan($plan, $subscriptionName)) {
                        throw new UserAlreadySubscribedToPlanException($plan);
                    }

                    $swapping = true;
                    $subscription->swap($plan);
                }
            }
        } catch (InvalidRequest $e) {
            if ($e->getHttpStatus() == 404) {
                $subscription = $this->newSubscription($actor, $subscriptionName, $plan, 0);
            } else {
                // Unsure of other expcetions that can be thrown.
                throw $e;
            }
        } catch (Exception $e) {
            throw $e;
        }

        $actor->trial_ends_at = null;
        $actor->save();

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
    public function newSubscription(User $actor, $subscriptionName, $plan, $trialDays = 0)
    {
        return $actor->newSubscription($subscriptionName, $plan)
            ->trialDays((int) $trialDays)
            ->create(null, [
                'email' => $actor->email,
                'id' => $actor->id,
            ]);
    }
}
