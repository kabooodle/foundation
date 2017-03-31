<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Listings;

use Bugsnag;
use Exception;
use Kabooodle\Models\User;
use Kabooodle\Models\Listings;
use Kabooodle\Services\Keen\KeenService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kabooodle\Bus\Events\Listings\ListingScheduledEvent;

/**
 * Class SendListingDataToKeen
 */
class SendListingDataToKeen implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var KeenService
     */
    public $keenService;

    /**
     * @param KeenService $keenService
     */
    public function __construct(KeenService $keenService)
    {
        $this->keenService = $keenService;
    }

    /***
     * @param ListingScheduledEvent $event
     */
    public function handle(ListingScheduledEvent $event)
    {
         try {
             $actor = User::findOrFail($event->getActorId());
             $listing = Listings::where('id', '=', $event->getListingId())
                 ->where('owner_id', $actor->id)
                 ->firstOrFail();

             $data = [
                 'id' => $listing->id,
                 'raw_data' => $listing,
                 'scheduled_for' => $listing->scheduled_for,
                 'type' => $listing->type,
                 'name' => $listing->name,
                 'sale_name' => $listing->sale_name,
                 'sale_morph_type' => $listing->morphedType,
                 'updated_at' => $listing->updated_at,
                 'created_at' => $listing->created_at,
                 'listing_items_count' => $listing->listingItems->count(),
                 'listables_count' => $listing->listables->count(),
                 'owner' => $listing->owner,
                 "keen" => [
                     "addons" => [
                         [
                             "name" => "keen:date_time_parser",
                             "input" => [ "date_time" => "updated_at.date" ],
                             "output" => "updated_at_timestamp"
                         ],
                         [
                             "name" => "keen:date_time_parser",
                             "input" => [ "date_time" => "scheduled_for.date" ],
                             "output" => "scheduled_for_timestamp"
                         ]
                     ]
                 ],
             ];

             $this->keenService->keenClient->addEvent('listings', $data);
         } catch (Exception $e) {
             Bugsnag::notifyException($e);
         }
    }
}
