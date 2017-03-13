<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\User;

use Bugsnag;
use Exception;
use Carbon\Carbon;
use Kabooodle\Models\Plans;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Kabooodle\Models\QualifiedReferrals;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kabooodle\Bus\Events\User\ReferralHasQualified;

/**
 * Class StoreQualifiedReferral
 */
class StoreQualifiedReferral implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param ReferralHasQualified $event
     */
    public function handle(ReferralHasQualified $event)
    {
        try {
            $actor = $event->getActor();
            $referredBy = $event->getReferredBy();
            $plan = $event->getPlan();

            $referral = new QualifiedReferrals;
            $referral->referred_by_id = $referredBy->id;
            $referral->referred_id = $actor->id;

            // Only apply coupons to the account if we don't yet have 6 coupons.
            if ($actor->qualifiedReferrals->count() < 6) {
                // apply coupon
                if (in_array($plan, Plans::getMonthlyPlans())){
                    $coupon = QualifiedReferrals::COUPON_1_MO_FREE;
                } else {
                    if ($plan == Plans::PLAN_MERCHANT_ANNUAL) {
                        $coupon = QualifiedReferrals::COUPON_1_MO_MERCHANT_ANNUAL_FREE;
                    } else {
                        $coupon = QualifiedReferrals::COUPON_1_MO_MERCHANT_PLUS_ANNUAL_FREE;
                    }
                }

                $referredBy->applyCoupon($coupon);

                $referral->stripe_coupon_id = $coupon;
                $referral->coupon_applied_at = Carbon::now();
            }

            $referral->group_hash = str_random();
            $referral->save();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }
    }
}
