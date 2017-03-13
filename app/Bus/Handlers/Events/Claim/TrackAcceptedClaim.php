<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Claim;

use Bugsnag;
use Exception;
use Kabooodle\Models\Claims;
use Illuminate\Bus\Queueable;
use KeenIO\Client\KeenIOClient;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kabooodle\Bus\Events\Claim\ClaimWasAcceptedEvent;

/**
 * Class TrackAcceptedClaim
 */
class TrackAcceptedClaim implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    /**
     * @var KeenIOClient
     */
    public $keen;

    /**
     * @param KeenIOClient $keen
     */
    public function __construct(KeenIOClient $keen)
    {
        $this->keen = $keen;
    }

    /**
     * @param ClaimWasAcceptedEvent $event
     *
     * @throws Exception
     */
    public function handle(ClaimWasAcceptedEvent $event)
    {
        try {
            /** @var Claims $claim */
            $claim = $event->getClaim();

            $data = [
                'id' => $claim->id,
                'uuid' => $claim->uuid,
                'listable_type' => $claim->listable_type,
                'listable_id' => $claim->listable_id,
                'listable_type' => $claim->listable->listable_type_friendly_name,
                'price' => $claim->price,
                'verified' => $claim->verified,
                'created_at' => $claim->created_at,
                'updated_at' => $claim->updated_at,

                // Objects
                'listing_sale_name' => $claim->listingItem->listing->sale_name,
                'listing_sale_type' => $claim->listingItem->listing->morphedType(),
                'listing' => $claim->listingItem->listing, // Listing
                'listing_item' => $claim->listingItem, // ListingItem
                'listable' => $claim->listable, // Listable object
                'listable_history' => $claim->listable_item_object_data, // Listable object snapshot
                'owner' => $claim->listable->owner, // User model
                'claimed_by' => $claim->claimedBy, // User model

                // Keen meta
                "keen" => [
                    "addons" => [
                        [
                            "name" => "keen:date_time_parser",
                            "input" => [ "date_time" => "updated_at.date" ],
                            "output" => "updated_at_timestamp"
                        ]
                    ]
                ],
            ];

            $this->keen->addEvent('accepted_claim', $data);
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }
    }
}
