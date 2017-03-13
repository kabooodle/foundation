<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Transformers\Flashsales;

use Kabooodle\Models\FlashSales;
use League\Fractal\TransformerAbstract;

/**
 * Class FlashsalesTransformer
 */
class FlashsalesTransformer extends TransformerAbstract
{
    /**
     * @param FlashSales $flashsale
     *
     * @return array
     */
    public function transform(FlashSales $flashsale)
    {
        return [
            'id' => $flashsale->id,
            'active' => $flashsale->active,
            'admins' => $flashsale->admins->pluck('id'),
            'owner' => $flashsale->owner->pluck('id'),
            'claimable_range' => $flashsale->claimable_range,
            'coverimage' => $flashsale->coverimage,
            'is_watched' => $flashsale->is_watched,
            'name' => $flashsale->name,
            'privacy' => $flashsale->privacy,
            'starts_at' => $flashsale->starts_at->toDateTimeString(),
            'ends_at' => $flashsale->ends_at->toDateTimeString(),
            'user_id' => $flashsale->user_id,
            'type' => $flashsale->type,
            'uuid' => $flashsale->uuid,
            'sellers' => $flashsale->sellers->count(),
            'listing_items' => $flashsale->listingItems->count(),
            'watchers' => $flashsale->watchers->count()
        ];
    }
}