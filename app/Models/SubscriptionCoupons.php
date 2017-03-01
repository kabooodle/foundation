<?php

namespace Kabooodle\Models;

/**
 * Class SubscriptionCoupons
 */
class SubscriptionCoupons extends BaseEloquentModel
{
    const COUPON_6_MO_FREE = '6_months_free_referral';
    const COUPON_5_MO_FREE = '5_months_free_referral';
    const COUPON_4_MO_FREE = '4_months_free_referral';
    const COUPON_3_MO_FREE = '3_months_free_referral';
    const COUPON_2_MO_FREE = '2_months_free_referral';
    const COUPON_1_MO_FREE = '1_months_free_referral';

    /**
     * @var string
     */
    protected $table = 'subscription_coupons';
}
