<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Subscriptions;

use Bugsnag;
use Exception;
use Carbon\Carbon;
use Kabooodle\Models\User;
use Illuminate\Support\Str;
use Kabooodle\Models\Plans;
use Stripe\Error\InvalidRequest;
use Laravel\Cashier\Subscription;
use Illuminate\Support\Collection;
use Kabooodle\Models\QualifiedReferrals;
use Kabooodle\Services\Referrals\ReferralsService;
use Kabooodle\Bus\Events\Profile\UserWasSubscribedToPlanEvent;
use Kabooodle\Bus\Commands\Subscriptions\SubscribeUserToPlanCommand;
use Kabooodle\Foundation\Exceptions\Subscription\UserAlreadySubscribedToPlanException;
use Kabooodle\Foundation\Exceptions\Subscription\UserHasNoCreditCardOnFileException;

/**
 * Class SubscribeUserToPlanCommandHandler
 */
class SubscribeUserToPlanCommandHandler
{
    /**
     * @var null|Collection
     */
    public $pendingQualifiedReferrals;

    /**
     * @var string
     */
    public $couponCodeUsed;

    /**
     * @var bool
     */
    public $poppingCherry = false;

    /**
     * Stores whether we are swapping the users' plan with a different one.
     * @var bool
     */
    public $swapping = false;

    /**
     * @var ReferralsService
     */
    public $referralsService;

    /**
     * @param ReferralsService $referralsService
     */
    public function __construct(ReferralsService $referralsService)
    {
        $this->referralsService = $referralsService;
    }

    /**
     * @param SubscribeUserToPlanCommand $command
     *
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
        $subscriptionName = $command->getSubscriptionName();

        // No card?!
        if (!$actor->getCard()) {
            throw new UserHasNoCreditCardOnFileException;
        }

        try {
            // Does the user have any subscriptions at all?
            if ($actor->subscriptions()->count() == 0) {
                // Create their first ever subscription to the plan!
                $subscription = $this->handleNewCustomer($actor, $subscriptionName, $plan);
            } else {
                $subscription = $this->handleExistingCustomer($actor, $subscriptionName, $plan);
            }

            // Cleanup
            if (in_array($plan, [Plans::PLAN_MERCHANTPLUS_ANNUAL, Plans::PLAN_MERCHANTPLUS_MONTH])) {
                $actor->kabooodle_as_shipping = 1;
            }

            $actor->trial_ends_at = null;
            $actor->save();

            event(new UserWasSubscribedToPlanEvent(
                    $actor,
                    $actor->currentSubscription(),
                    $plan,
                    $this->poppingCherry,
                    $this->swapping)
            );

            return $subscription;
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            throw $e;
        }
    }

    /**
     * @param User   $actor
     * @param string $subscriptionName
     * @param string $plan
     *
     * @return $this
     */
    public function handleNewCustomer(User $actor, string $subscriptionName, string $plan)
    {
        $this->poppingCherry = true;

        $subscription = $actor->newSubscription($subscriptionName, $plan)->trialDays(0);

        // Check if there are any pending referrals
        $referrals = $this->pendingQualifiedReferrals;

        if ($referrals && $referrals->count() > 0) {
            // Monthly plans, can only have 1 coupon applied.  So we only retrieve the first referral.
            if (in_array($plan, Plans::getMonthlyPlans())) {
                $couponReferrals = collect([$referrals->first()]);
            } else {
                $couponReferrals = $referrals->chunk(6)[0];
            }

            $remainingReferrals = $referrals->filter(function ($referral) use ($couponReferrals) {
                return !in_array($referral->id, $couponReferrals->pluck('id')->toArray());
            });

            // Determines the coupon name based on the plan and number of pending referrals.
            // for example, an annual subscription with 3 pending referrals would yield a discount of 3 months for the 12.
            // for example, a monthly subscription with 3 pending referrals would yield an immediate discount of 1 month and
            // add the remaining 2 to the user for the next time the subscription renews.
            $coupon = $this->getApplicableReferralCouponForBrandNewSubscriber($couponReferrals, $plan);
            $subscription = $subscription->withCoupon($coupon);

            // Update the referrals database, flagging pending referrals as having been used.
            $this->markUsedReferralsAsAppliedForUser($referrals, $coupon);
        }

        // Create our subscription
        $subscription->create(null, [
            'email' => $actor->email,
            'id' => $actor->id,
        ]);

        // In the event a coupon was used and we still have remaining referrals that we didn't use (i.e. a monthly subscription),
        // then apply the remaining referrals as a coupon for each month.
        if (isset($coupon) && $remainingReferrals->count() > 0) {
            for ($i = 0; $i < $remainingReferrals->count(); $i++) {
                $actor->applyCoupon($coupon);
            }
        }

        return $subscription;
    }

    /**
     * @param User        $actor
     * @param string      $subscriptionName
     * @param             $plan
     *
     * @return Subscription
     * @throws UserAlreadySubscribedToPlanException
     */
    public function handleExistingCustomer(User $actor, string $subscriptionName, $plan)
    {
        // At this point, the user is clearly subscribed to SOME SORT OF plan
        // We need to determine if the current plan they have has been cancelled but is on grace period
        // If so, we will just resume their subscription.
        // Otherwise, we will swap their existing with the new subscription.

        // We need to know which pendingReferrals are being applied, and which remain as is.
        $referrals = $this->getPendingAndApplicableQualifiedReferrals($actor, $plan);

        if ($referrals && $referrals->count() > 0) {
            if (in_array($plan, Plans::getMonthlyPlans())) {
                $couponReferrals = collect([$referrals->first()]);
            } else {
                $couponReferrals = $referrals->chunk(6)[0];
            }

            $remainingReferrals = $referrals->filter(function ($referral) use ($couponReferrals) {
                return !in_array($referral->id, $couponReferrals->pluck('id')->toArray());
            });

            // We need to know which coupon we are applying
            $coupon = $this->getApplicableReferralCouponForBrandNewSubscriber($couponReferrals, $plan);

            $actor->applyCoupon($coupon);

            $this->markUsedReferralsAsAppliedForUser($referrals, $coupon);

            // In the event a coupon was used and we still have remaining referrals that we didn't use (i.e. a monthly subscription),
            // then apply the remaining referrals as a coupon for each month.
            if (isset($coupon) && $remainingReferrals->count() > 0) {
                for ($i = 0; $i < $remainingReferrals->count(); $i++) {
                    $actor->applyCoupon($coupon);
                }
            }
        }


        /** @var Subscription $subscription */
        $subscription = $actor->currentSubscription();
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

            $this->swapping = true;

            $subscription->swap($plan);

            return $subscription;
        }
    }

    /**
     * @param User $actor
     *
     * @return array|void
     */
    public function getPendingAndApplicableQualifiedReferrals(User $actor)
    {
        $pendingQualifiedReferrals = $actor->pendingQualifiedReferrals;
        if ($pendingQualifiedReferrals->count() > 0) {
            $chunk = $pendingQualifiedReferrals->chunk(6);

            return $chunk[0];
        }

        return collect([]);
    }

    /**
     * @param Collection $referrals
     * @param string     $plan
     *
     * @return null|string
     */
    public function getApplicableReferralCouponForBrandNewSubscriber($referrals, string $plan)
    {
        return $this->referralsService->getApplicableReferralCouponForBrandNewSubscriber($referrals, $plan);
    }

    /**
     * @param Collection $referrals
     * @param string     $couponCode
     */
    public function markUsedReferralsAsAppliedForUser(Collection $referrals, string $couponCode)
    {
        foreach ($referrals as $referral) {
            $this->referralsService->markUsedReferralAsAppliedForUser($referral, $couponCode);
        }
    }
}
