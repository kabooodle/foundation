<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Transformers\Claims;

use stdClass;
use League\Fractal\TransformerAbstract;

/**
 * Class ClaimsMerchantTransformer
 */
class ClaimsMerchantTransformer extends TransformerAbstract
{
    /**
     * @param $claims
     *
     * @return array
     */
    public function transform(stdClass $claims)
    {
        $claims = json_decode(json_encode($claims), true);
        $claims['listable_cover_photo_location'] = useCDN() ? staticAsset($claims['listable_cover_photo_key'], false) : $claims['listable_cover_photo_location'];
        $claims['rejected'] = (bool) ($claims['rejected_on'] !== null);
        $claims['profile_endpoint'] = $claims['username'] ? route('user.profile', [$claims['username']]) : false;

        return $claims;
    }
}