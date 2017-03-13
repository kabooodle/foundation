<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use ReflectionClass;

/**
 * Class Plans
 * @package Kabooodle\Models\Plans
 */
final class Plans
{
    const SUBSCRIPTION_MERCHANT = 'merchant';
    const SUBSCRIPTION_MERCHANT_PLUS = 'merchant_plus';

    const PLAN_EARLY_ADOPTER = 'kabooodle_launch_plan';

    const PLAN_MERCHANTPLUS_ANNUAL = 'merchant_plus_annual';
    const PLAN_MERCHANTPLUS_MONTH = 'merchant_plus_monthly';

    const PLAN_MERCHANT_ANNUAL = 'merchant_annual';
    const PLAN_MERCHANT_MONTH = 'merchant_monthly';

    /**
     * @var array
     */
    public static $commonFeatures = [
        'Unlimited Inventory Items',
        'Unlimited Images',
        'Unlimited Flash Sales',
        'Unlimited Facebook Sales',
        'Sales and Item Analytics',
        'Overselling Prevention',
        'iOS iPhone App',
        'Custom Social Features'
    ];

    /**
     * @var array
     */
    public static $merchantPlusFeatures = [
        '<strong>Includes All Merchant Plan Features</strong>',
        '+',
        'Ship Directly Through USPS',
        'Print Shipping Labels',
        'Track Shipments Anywhere',
        'Shipment Tracking Notifications',
        'No Hidden Fees',
    ];

    /**
     * @return array
     */
    public static function getMonthlyPlans()
    {
        return [
            self::PLAN_EARLY_ADOPTER,
            self::PLAN_MERCHANT_MONTH,
            self::PLAN_MERCHANTPLUS_MONTH
        ];
    }

    /**
     * @return mixed
     */
    public static function getAllPlans()
    {
        return [
            static::PLAN_EARLY_ADOPTER,

            static::PLAN_MERCHANTPLUS_ANNUAL,
            static::LAN_MERCHANTPLUS_MONTH,

            static::PLAN_MERCHANT_ANNUAL,
            static::PLAN_MERCHANT_MONTH
        ];
    }

    /**
     * @param string $interval
     * @return mixed
     */
    public static function getMerchantPlan($interval = 'monthly')
    {
        $data = [
            'month' => [
                'interval' => 'month',
                'price' => '10',
                'id' => static::PLAN_MERCHANT_MONTH
            ],
            'annual' => [
                'interval' => 'year',
                'price' => '96',
                'id' => static::PLAN_MERCHANT_ANNUAL
            ]
        ];

        return $data[$interval];
    }

    /**
     * @param string $interval
     *
     * @return mixed
     */
    public static function getMerchantPlusPlan($interval = 'monthly')
    {
        $data = [
            'month' => [
                'interval' => 'month',
                'price' => '15',
                'id' => static::PLAN_MERCHANTPLUS_MONTH
            ],
            'annual' => [
                'interval' => 'year',
                'price' => '144',
                'id' => static::PLAN_MERCHANTPLUS_ANNUAL
            ]
        ];

        return $data[$interval];
    }

    /**
     * @param $planId
     * @return array
     */
    public static function getPlan($planId)
    {
        if (in_array($planId, [static::PLAN_MERCHANT_ANNUAL, static::PLAN_MERCHANT_MONTH])) {
            $interval = $planId == static::PLAN_MERCHANT_ANNUAL ? 'annual' : 'month';
            return [
                static::getMerchantPlan($interval),
                static::getMerchantPlanGroup()
            ];
        }

        if (in_array($planId, [static::PLAN_MERCHANTPLUS_ANNUAL, static::PLAN_MERCHANTPLUS_MONTH])) {
            $interval = $planId == static::PLAN_MERCHANTPLUS_ANNUAL ? 'annual' : 'month';
            return [
                static::getMerchantPlusPlan($interval),
                static::getMerchantPlusPlanGroup()
            ];
        }

        if($planId === static::PLAN_EARLY_ADOPTER) {
            return [
                static::getEarlyAdapterPlan(),
                static::getEarlyAdapterPlan(),
            ];
        }

        throw new \InvalidArgumentException();
    }

    /**
     * @return array
     */
    public static function getEarlyAdapterPlan()
    {
        $features = static::$commonFeatures;
        $plusFeatures = static::$merchantPlusFeatures;
        unset($plusFeatures[0]);

        return [
            'subscription' => 'merchant_plus',
            'name' => 'Early Adapter Plan',
            'image' => '/assets/images/early_adapter_plan.jpg',
            'features' => array_merge($features, $plusFeatures),
            'interval' => 'month',
            'price' => 5,
            'id' => 'kabooodle_launch_plan'
        ];
    }

    /**
     * @return array
     */
    public static function getMerchantPlanGroup()
    {
        return [
            'subscription' => 'merchant',
            'name' => 'Merchant Plan',
            'image' => '/assets/images/merchant_plan.jpg',
            'features' => static::$commonFeatures,
            'monthly' => static::getMerchantPlan('month'),
            'annual' => static::getMerchantPlan('annual')
        ];
    }

    /**
     * @return array
     */
    public static function getMerchantPlusPlanGroup()
    {
        return [
            'subscription' => 'merchant_plus',
            'name' => 'Merchant Plus Plan',
            'image' => '/assets/images/merchant_plus_plan.jpg',
            'features' => static::$merchantPlusFeatures,
            'monthly' => static::getMerchantPlusPlan('month'),
            'annual' => static::getMerchantPlusPlan('annual')
        ];
    }
}
