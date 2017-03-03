<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Subscriptions;

use Bugsnag;
use Exception;
use Carbon\Carbon;
use Kabooodle\Models\Plans;
use Kabooodle\Models\User;
use Illuminate\Support\Str;
use Kabooodle\Models\Referrals;
use Stripe\Error\InvalidRequest;
use Laravel\Cashier\Subscription;
use Illuminate\Database\Eloquent\Collection;
use Kabooodle\Services\Referrals\ReferralsService;
use Kabooodle\Bus\Events\Profile\UserWasSubscribedToPlanEvent;
use Kabooodle\Bus\Commands\Subscriptions\SubscribeUserToPlanCommand;
use Kabooodle\Foundation\Exceptions\Subscription\UserHasNoCreditCardOnFileException;
use Kabooodle\Foundation\Exceptions\Subscription\UserAlreadySubscribedToPlanException;
use Stripe\Plan;

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

            // We need to know which coupon we are applying
            // We need to know which pendingReferrals are being applied, and which remain as is.
            $coupons = $this->getApplicableReferralCouponForBrandNewSubscriber($actor, $plan);

            // Does the user have any subscriptions at all?
            if ($actor->subscriptions()->count() == 0) {
                // Create their first ever subscription to the plan!
                $subscription = $this->handleNewCustomer($actor, $subscriptionName, $plan);
            } else {
                $subscription = $this->handleExistingCustomer($actor, $subscriptionName, $plan);
            }

            // Cleanup
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
     * Creates a new Stripe Customer
     *
     * @param User   $actor
     * @param string $subscriptionName
     * @param string $plan
     * @param int    $trialDays
     *
     * @return $this|Subscription
     */
    public function handleNewCustomer(User $actor, string $subscriptionName, $plan, int $trialDays = 0)
    {
        $this->poppingCherry = true;

        $subscription = $actor->newSubscription($subscriptionName, $plan)->trialDays((int) $trialDays);

        if ($coupon = $this->getApplicableReferralCouponForBrandNewSubscriber($actor, $plan)) {
            $subscription = $subscription->withCoupon($coupon);
        }

        $subscription->create(null, [
            'email' => $actor->email,
            'id' => $actor->id,
        ]);

        $this->markPendingQualifiedReferralsAsApplied();

        return $subscription;
    }

    /**
     * Updates existing Stripe Customer's subscription
     *
     * @param User         $actor
     * @param string       $subscriptionName
     * @param string       $plan
     * @param bool         $skipTrial
     *
     * @return Subscription|mixed
     * @throws UserAlreadySubscribedToPlanException
     */
    public function handleExistingCustomer(User $actor, string $subscriptionName, $plan, bool $skipTrial = true)
    {
        // At this point, the user is clearly subscribed to SOME SORT OF plan
        // We need to determine if the current plan they have has been cancelled but is on grace period
        // If so, we will just resume their subscription.
        // Otherwise, we will swap their existing with the new subscription.

        /** @var Subscription $subscription */
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

            $this->swapping = true;

            // Check for coupon
            $subscription->swap($plan);

            return $subscription;
        }
    }

    /**
     * @param User $actor
     * @param string $plan
     * @return array|void
     */
    public function getPendingAndApplicableQualifiedReferrals(User $actor, string $plan)
    {
        $pendingQualifiedReferrals = $actor->pendingQualifiedReferrals;
        if ($pendingQualifiedReferrals->count() > 0) {
            if (in_array($plan , Plans::getMonthlyPlans())) {
                // For monthly plans, we only use 1 qualified referral, which will be applied to their initial month.
                return $pendingQualifiedReferrals->first();
            } else {
                // Because we only allow a max of 6 coupons ever, we only allow a new annual plan be discounted by 6 coupons.
                // Which is also a 6 month discount.
                $chunk = $pendingQualifiedReferrals->chunk(6);

                return $chunk[0];
            }
        }

        return collect([]);
    }

    /**
     * The user may have unused coupons that we need to associate to their FIRST time subscription.
     * Currently, these coupons are only based on referrals and nothing more.
     *
     * @param User   $actor
     * @param string $plan
     * @return null|string
     */
    public function getApplicableReferralCouponForBrandNewSubscriber(User $actor, string $plan)
    {
        $count = $this->getPendingAndApplicableQualifiedReferrals($actor, $plan);

        if ($count > 0) {
            // If the user signed up for a year long account,
            // we need to discount their subscription...
            if ($plan == Plans::PLAN_MERCHANTPLUS_ANNUAL) {
                // Only allow max of 6 coupons to be redeemed for referrals
                if ($count >= 6) {
                    $couponCode = Referrals::COUPON_6_MO_MERCHANT_PLUS_ANNUAL_FREE;
                } elseif ($count == 5) {
                    $couponCode = Referrals::COUPON_5_MO_MERCHANT_PLUS_ANNUAL_FREE;
                } elseif ($count == 4) {
                    $couponCode = Referrals::COUPON_4_MO_MERCHANT_PLUS_ANNUAL_FREE;
                } elseif ($count == 3) {
                    $couponCode = Referrals::COUPON_3_MO_MERCHANT_PLUS_ANNUAL_FREE;
                } elseif ($count == 2) {
                    $couponCode = Referrals::COUPON_2_MO_MERCHANT_PLUS_ANNUAL_FREE;
                } else {
                    $couponCode = Referrals::COUPON_1_MO_MERCHANT_PLUS_ANNUAL_FREE;
                }
            } elseif ($plan == PLANS::PLAN_MERCHANT_ANNUAL) {
                if ($count >= 6) {
                    $couponCode = Referrals::COUPON_6_MO_MERCHANT_ANNUAL_FREE;
                } elseif ($count == 5) {
                    $couponCode = Referrals::COUPON_5_MO_MERCHANT_ANNUAL_FREE;
                } elseif ($count == 4) {
                    $couponCode = Referrals::COUPON_4_MO_MERCHANT_ANNUAL_FREE;
                } elseif ($count == 3) {
                    $couponCode = Referrals::COUPON_3_MO_MERCHANT_ANNUAL_FREE;
                } elseif ($count == 2) {
                    $couponCode = Referrals::COUPON_2_MO_MERCHANT_ANNUAL_FREE;
                } else {
                    $couponCode = Referrals::COUPON_1_MO_MERCHANT_ANNUAL_FREE;
                }
            } else {
                $couponCode = Referrals::COUPON_1_MO_FREE;
            }

            $this->couponCodeUsed = $couponCode;

            return $couponCode;
        }

        return null;
    }

    /**
     * @param array $ids
     */
    public function markPendingQualifiedReferralsAsApplied(array $ids = [])
    {
        if (! $this->pendingQualifiedReferrals || $this->pendingQualifiedReferrals->count() == 0) {
            return;
        }

        $timestamp = Carbon::now();
        $groupHash = Str::random();

        foreach ($this->pendingQualifiedReferrals as $pendingReferral) {
            $pendingReferral->coupon_applied_at = $timestamp;
            $pendingReferral->group_hash = $groupHash;
            $pendingReferral->stripe_coupon_id = $this->couponCodeUsed;
            $pendingReferral->save();
        }
    }
}
