<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Transformers\Listings;

use Kabooodle\Models\Listings;
use League\Fractal\TransformerAbstract;

/**
 * Class UserListingsTransformer
 */
class UserListingsTransformer extends TransformerAbstract
{
    public function transform(Listings $listing)
    {
        return [
            'claimable_range' => $listing->claimable_range,
            'claimable_at' => $listing->claimable_at,
            'claimable_until' => $listing->claimable_until,
            'created_at' => $listing->created_at,
            'id' => $listing->id,
            'items_count' => $listing->items_count,
            'type' => $listing->type,
            'sale_name' => $listing->sale_name,
            'scheduled_for' => $listing->scheduled_for,
            'status' => $listing->status,
            'uuid' => $listing->uuid,
            'type_icon_src' => $listing->type_icon_src,
            'endpoint' => route('listings.show', [$listing->uuid]),
        ];
    }
}