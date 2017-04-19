<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Transformers\Claims;

use stdClass;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;
use Kabooodle\Models\ShippingTransactions;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;

/**
 * Class ClaimsMerchantTransformer
 */
class ClaimsMerchantTransformer extends TransformerAbstract
{
    use ObfuscatesIdTrait;

    /**
     * @var bool
     */
    public $userHasMerchantPlus = false;

    /**
     * @var ShippingTransactions
     */
    public $shippingEntity;

    public function __construct()
    {
        if (user() && user()->isSubscribedToMerchantPlus()) {
            $this->userHasMerchantPlus = true;
            $this->shippingEntity = new ShippingTransactions;
        }
    }

    /**
     * @param $claims
     *
     * @return array
     */
    public function transform(stdClass $claims)
    {
        $claims = json_decode(json_encode($claims), true);
        $claims['listable_price'] = currency($claims['listable_price']);
        $claims['price'] = currency($claims['price']);
        $claims['listable_cover_photo_location'] = useCDN() ? staticAsset($claims['listable_cover_photo_key'], false) : $claims['listable_cover_photo_location'];
        $claims['rejected'] = (bool) ($claims['rejected_on'] !== null);
        $claims['profile_endpoint'] = $claims['username'] ? route('user.profile', [$claims['username']]) : false;
        $claims['claim_created_at'] = Carbon::parse($claims['claim_created_at'])->setTimezone(current_timezone());
        $claims['listing_item_endpoint'] = route('listingitems.show', [$this->obfuscateIdToString($claims['listing_id'])]);
        $claims['listing_endpoint'] = route('listings.show', [$claims['listing_uuid']]);
        $claims['shipping_create_endpoint'] = $this->userHasMerchantPlus ? route('merchant.shipping.create').'?c='.$claims['id'] : null;
        $claims['is_merchant_plus'] = $this->userHasMerchantPlus;
        $claims['shipping_label_endpoint'] = $this->userHasMerchantPlus && $claims['shipping_transaction_uuid'] != null ? route('merchant.shipping.transactions.label.show', [$claims['shipping_shipment_uuid'], $claims['shipping_transaction_uuid']]) : null;
        $claims['shipping_transaction_endpoint'] = $this->userHasMerchantPlus && $claims['shipping_transaction_uuid'] != null  ? route('merchant.shipping.transactions.show', [$claims['shipping_shipment_uuid'], $claims['shipping_transaction_uuid']]) : null;

        if ($this->shippingEntity && $claims['shipped_via_kabooodle'] == 1) {
            $claims['shipping_status'] = $this->shippingEntity->present()->mapStatusAndReturnLink($claims['shipping_status']);
        }

        return $claims;
    }
}
