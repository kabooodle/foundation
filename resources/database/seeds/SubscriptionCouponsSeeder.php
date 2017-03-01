<?php

use Illuminate\Database\Seeder;

class SubscriptionCouponsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \Kabooodle\Models\SubscriptionCoupons::truncate();
        \Kabooodle\Models\SubscriptionCoupons::forceCreate([
            'id' => 1,
            'name' => '6 Free months for 6 qualifying referrals',
            'coupon_id' => '6_months_free_referral',
            'coupon_amount_off' => '100',
            'type' => 'percent',
        ]);
        \Kabooodle\Models\SubscriptionCoupons::forceCreate([
            'id' => 2,
            'name' => '5 Free months for 5 qualifying referrals',
            'coupon_id' => '5_months_free_referral',
            'coupon_amount_off' => '100',
            'type' => 'percent',
        ]);
        \Kabooodle\Models\SubscriptionCoupons::forceCreate([
            'id' => 3,
            'name' => '4 Free months for 4 qualifying referrals',
            'coupon_id' => '4_months_free_referral',
            'coupon_amount_off' => '100',
            'type' => 'percent',
        ]);
        \Kabooodle\Models\SubscriptionCoupons::forceCreate([
            'id' => 4,
            'name' => '3 Free months for 3 qualifying referrals',
            'coupon_id' => '3_months_free_referral',
            'coupon_amount_off' => '100',
            'type' => 'percent',
        ]);
        \Kabooodle\Models\SubscriptionCoupons::forceCreate([
            'id' => 5,
            'name' => '2 Free months for 2 qualifying referrals',
            'coupon_id' => '2_months_free_referral',
            'coupon_amount_off' => '100',
            'type' => 'percent',
        ]);
        \Kabooodle\Models\SubscriptionCoupons::forceCreate([
            'id' => 6,
            'name' => 'Free month for a qualifying referral',
            'coupon_id' => '1_months_free_referral',
            'coupon_amount_off' => '100',
            'type' => 'percent',
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
