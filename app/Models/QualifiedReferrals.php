<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Sofa\Revisionable\Revisionable;
use Kabooodle\Models\Traits\EloquentDatesTrait;
use Sofa\Revisionable\Laravel\RevisionableTrait;

/**
 * Class QualifiedReferrals
 */
class QualifiedReferrals extends BaseEloquentModel implements Revisionable
{
    use RevisionableTrait;

    const COUPON_6_MO_FREE = '6_months_free_referral';
    const COUPON_5_MO_FREE = '5_months_free_referral';
    const COUPON_4_MO_FREE = '4_months_free_referral';
    const COUPON_3_MO_FREE = '3_months_free_referral';
    const COUPON_2_MO_FREE = '2_months_free_referral';
    const COUPON_1_MO_FREE = '1_months_free_referral';

    const COUPON_6_MO_MERCHANT_PLUS_ANNUAL_FREE = '6_months_free_merchant_plus_annual';
    const COUPON_5_MO_MERCHANT_PLUS_ANNUAL_FREE = '5_months_free_merchant_plus_annual';
    const COUPON_4_MO_MERCHANT_PLUS_ANNUAL_FREE = '4_months_free_merchant_plus_annual';
    const COUPON_3_MO_MERCHANT_PLUS_ANNUAL_FREE = '3_months_free_merchant_plus_annual';
    const COUPON_2_MO_MERCHANT_PLUS_ANNUAL_FREE = '2_months_free_merchant_plus_annual';
    const COUPON_1_MO_MERCHANT_PLUS_ANNUAL_FREE = '1_months_free_merchant_plus_annual';

    const COUPON_6_MO_MERCHANT_ANNUAL_FREE = '6_months_free_merchant_annual';
    const COUPON_5_MO_MERCHANT_ANNUAL_FREE = '5_months_free_merchant_annual';
    const COUPON_4_MO_MERCHANT_ANNUAL_FREE = '4_months_free_merchant_annual';
    const COUPON_3_MO_MERCHANT_ANNUAL_FREE = '3_months_free_merchant_annual';
    const COUPON_2_MO_MERCHANT_ANNUAL_FREE = '2_months_free_merchant_annual';
    const COUPON_1_MO_MERCHANT_ANNUAL_FREE = '1_months_free_merchant_annual';

    /**
     * @var array
     */
    protected $dates = [
        'coupon_applied_at'
    ];

    /**
     * @var string
     */
    protected $table = 'qualified_referrals';

    /**
     * @var array
     */
    protected $fillable = [
        'referred_by_id',
        'referred_id',
        'stripe_coupon_id',
        'group_hash',
        'coupon_applied_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referredBy()
    {
        return $this->belongsto(User::class, 'referred_by_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referred()
    {
        return $this->belongsto(User::class, 'referred_id');
    }

    /**
     * @return bool
     */
    public function couponApplied()
    {
        return $this->coupon_applied_at && !is_null($this->coupon_applied_at);
    }
}
