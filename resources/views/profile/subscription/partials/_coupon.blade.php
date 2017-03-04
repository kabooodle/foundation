<?php $getPendingQualifiedReferralTotal = webUser()->getPendingQualifiedReferralTotal(); ?>
<div class="box primary">
    <div class="box-body">
        <p class="m-b-0">
            <strong>
                Coupon for {{ $getPendingQualifiedReferralTotal }} free
                {{ str_plural('month', $getPendingQualifiedReferralTotal) }} will be applied at checkout.
                Thank you for the {{ str_plural('referral', $getPendingQualifiedReferralTotal) }}!
            </strong>
        </p>
    </div>
</div>