<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Listings;

use Log;
use Bugsnag;
use Carbon\Carbon;
use Kabooodle\Models\User;
use Kabooodle\Models\ListingItems;
use Kabooodle\Services\Social\Facebook\FacebookSdkService;
use Kabooodle\Bus\Commands\Listings\DeleteListingItemFromFacebookCommand;

/**
 * Class DeleteListingItemFromFacebookCommandHandler
 */
class DeleteListingItemFromFacebookCommandHandler
{
    public $facebookService;

    /**
     * @param FacebookSdkService $facebookService
     */
    public function __construct(FacebookSdkService $facebookService)
    {
        $this->facebookService = $facebookService;
    }

    /**
     * @param DeleteListingItemFromFacebookCommand $command
     *
     * @return void
     */
    public function handle(DeleteListingItemFromFacebookCommand $command)
    {
        $listingItem = ListingItems::where('listing_id', '=', $command->getListingId())
            ->where('id', '=', $command->getListingItemId())
            ->where('owner_id', '=', $command->getOwnerId())
            ->where('type', '=', ListingItems::TYPE_FACEBOOK)
            ->first();

        $logString = '[Listing:  id: ['.$command->getListingItemId().'] , listing_id: ['.$command->getListingId().'], owner_id: ['.$command->getOwnerId().']';

        // Listing item not found
        if (! $listingItem) {
            Log::error('Deleting listing item from facebook: Listing item not found based on constraints - '.$logString);
            $command->delete();

            return;
        }

        // Listing item status is deleted or queued for deletion
        if ($listingItem->status == ListingItems::STATUS_DELETED) {
            Log::error('Deleting listing item from facebook: Listing item already (queued) deleted - '.$logString);
            $command->delete();

            return;
        }

        // Listing item does not have facebook id
        if (! $listingItem->fb_response_object_id || $listingItem->fb_response_object_id == null) {
            Log::error('Deleting listing item from facebook: There is no facebook item - '.$logString);
            $listingItem->status = ListingItems::STATUS_DELETED;
            $listingItem->status_updated_at = Carbon::now();
            $listingItem->status_history = 'Listing item possibly already deleted or not listed.';
            $listingItem->save();

            $command->delete();

            return;
        }

        /** @var User $owner */
        $owner = User::find($command->getOwnerId());

        // If the access token has expired, then we're fucked because it IS a long-lived token
        // so the only way to generate a new one, is to login again on the client side.
        // The user needs to do this.
        //
//        if (! $this->facebookService->testAccessToken($owner->getFacebookUserToken())) {
//            Log::error('Deleting listing item from facebook: User facebook token expired or invalid - '.$logString);
//            $listingItem->status = ListingItems::STATUS_FAILED_DELETE;
//            $listingItem->status_updated_at = Carbon::now();
//            $listingItem->status_history = 'Facebook access token expired or invalid.';
//            $listingItem->save();
//
//            $command->delete();
//
//            return;
//        }


        try {
            $this->facebookService->deletePhoto($listingItem->fb_response_object_id, [], $owner->getFacebookUserToken());

        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }

        $listingItem->status = ListingItems::STATUS_DELETED;
        $listingItem->status_updated_at = Carbon::now();
        $listingItem->status_history = '';
        $listingItem->save();

        $command->delete();
    }
}
