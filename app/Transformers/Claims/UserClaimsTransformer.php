<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Transformers\Claims;

use Kabooodle\Models\Claims;
use League\Fractal\TransformerAbstract;

/**
 * Class UserClaimsTransformer
 */
class UserClaimsTransformer extends TransformerAbstract
{
    public function transform(Claims $claim)
    {
        return [
            'id' => $claim->id,
            'status' => $claim->present()->getClaimStatus(),
            'price' => currency($claim->price),
            'cancelable' => $claim->isCancelable(),
            'canceled_at' => $claim->canceled_at,
            'shipping_status' => $claim->present()->getShippingStatus($statusAsBuyerPov = true),
            'view_route' => route('profile.claims.show', [$claim->getUUID()]),
            'created_at_human' => $claim->createdAtHumanNoTime(),
            'created_at' => $claim->created_at,
            'accepted' => $claim->wasAccepted(),
            'rejected' => $claim->wasRejected(),
            'item' => [
                'image' => $claim->listedItem->cover_photo->location,
                'title' => $claim->listedItem->title,
                'owner' => [
                    'username' => $claim->listedItem->owner->username,
                    'view_route' => route('user.profile', $claim->listedItem->owner->username),
                ]
            ],
            'listing' => [
                'name' => $claim->listingItem->sale_name,
                'view_route' => $claim->listingItem->listing ? route('listings.show', $claim->listingItem->listing->uuid) : null,
            ],
        ];
    }
}