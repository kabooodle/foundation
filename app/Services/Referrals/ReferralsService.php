<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Services\Referrals;

use Carbon\Carbon;
use Kabooodle\Models\User;
use Kabooodle\Models\Plans;
use Kabooodle\Models\QualifiedReferrals;
use Kabooodle\Services\User\UserService;

/**
 * Class ReferralsService
 */
class ReferralsService
{
    const REFERRAL_BY_USERNAME = 'kbdl_referrer_username';

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param $username
     *
     * @return mixed
     */
    public function lookupRefereeByUsername($username)
    {
        return $this->userService->repository->getByUsername($username);
    }

    /**
     * @return null|User
     */
    public function getReferral()
    {
        return session(self::REFERRAL_BY_USERNAME);
    }

    /**
     * @param Collection $referrals
     * @param string     $plan
     *
     * @return null|string
     */
    public function getApplicableReferralCouponForBrandNewSubscriber(Collection $referrals, string $plan)
    {
        $count = $referrals->count();
        if ($count > 0) {
            // If the user signed up for a year long account,
            // we need to discount their subscription...
            if ($plan == Plans::PLAN_MERCHANTPLUS_ANNUAL) {
                // Only allow max of 6 coupons to be redeemed for referrals
                if ($count >= 6) {
                    $couponCode = QualifiedReferrals::COUPON_6_MO_MERCHANT_PLUS_ANNUAL_FREE;
                } elseif ($count == 5) {
                    $couponCode = QualifiedReferrals::COUPON_5_MO_MERCHANT_PLUS_ANNUAL_FREE;
                } elseif ($count == 4) {
                    $couponCode = QualifiedReferrals::COUPON_4_MO_MERCHANT_PLUS_ANNUAL_FREE;
                } elseif ($count == 3) {
                    $couponCode = QualifiedReferrals::COUPON_3_MO_MERCHANT_PLUS_ANNUAL_FREE;
                } elseif ($count == 2) {
                    $couponCode = QualifiedReferrals::COUPON_2_MO_MERCHANT_PLUS_ANNUAL_FREE;
                } else {
                    $couponCode = QualifiedReferrals::COUPON_1_MO_MERCHANT_PLUS_ANNUAL_FREE;
                }
            } elseif ($plan == PLANS::PLAN_MERCHANT_ANNUAL) {
                if ($count >= 6) {
                    $couponCode = QualifiedReferrals::COUPON_6_MO_MERCHANT_ANNUAL_FREE;
                } elseif ($count == 5) {
                    $couponCode = QualifiedReferrals::COUPON_5_MO_MERCHANT_ANNUAL_FREE;
                } elseif ($count == 4) {
                    $couponCode = QualifiedReferrals::COUPON_4_MO_MERCHANT_ANNUAL_FREE;
                } elseif ($count == 3) {
                    $couponCode = QualifiedReferrals::COUPON_3_MO_MERCHANT_ANNUAL_FREE;
                } elseif ($count == 2) {
                    $couponCode = QualifiedReferrals::COUPON_2_MO_MERCHANT_ANNUAL_FREE;
                } else {
                    $couponCode = QualifiedReferrals::COUPON_1_MO_MERCHANT_ANNUAL_FREE;
                }
            } else {
                $couponCode = QualifiedReferrals::COUPON_1_MO_FREE;
            }

            return $couponCode;
        }

        return null;
    }

    /**
     * @param QualifiedReferrals $referral
     * @param string             $couponCode
     *
     * @return QualifiedReferrals
     */
    public function markUsedReferralAsAppliedForUser(QualifiedReferrals $referral, string $couponCode)
    {
        $timestamp = Carbon::now();
        $groupHash = str_random();

        $referral->coupon_applied_at = $timestamp;
        $referral->group_hash = $groupHash;
        $referral->stripe_coupon_id = $couponCode;
        $referral->save();

        return $referral;
    }
}
