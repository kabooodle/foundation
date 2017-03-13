<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Listings;

use Log;
use Carbon\Carbon;
use Kabooodle\Models\User;
use Kabooodle\Models\Listings;
use Kabooodle\Libraries\QueueHelper;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Services\Social\Facebook\FacebookSdkService;
use Kabooodle\Bus\Commands\Listings\DeleteListingFromFacebookCommand;
use Kabooodle\Bus\Commands\Listings\DeleteListingItemFromFacebookCommand;

/**
 * Class DeleteListingFromFacebookCommandHandler
 */
class DeleteListingFromFacebookCommandHandler
{
    use DispatchesJobs;

    /**
     * @var FacebookSdkService
     */
    public $facebookService;

    /**
     * @param FacebookSdkService $facebookService
     */
    public function __construct(FacebookSdkService $facebookService)
    {
        $this->facebookService = $facebookService;
    }

    /**
     * Make sure this command/handler were dispatching from a facebook scheduler command!
     *
     * @param DeleteListingFromFacebookCommand $command
     */
    public function handle(DeleteListingFromFacebookCommand $command)
    {
        $listing = Listings::where('id', '=', $command->getListingId())
            ->where('owner_id', '=', $command->getOwnerId())
            ->where('type', '=', Listings::TYPE_FACEBOOK)
            ->first();

        $logString = '[Listing:  id: ['.$command->getListingId().'], owner_id: ['.$command->getOwnerId().']';

        // Listing item not found
        if (! $listing) {
            Log::error('Deleting listing from facebook: Listing not found based on constraints - '.$logString);
            $command->delete();

            return;
        }

        /** @var User $owner */
        $owner = User::find($command->getOwnerId());

        // If the access token has expired, then we're fucked because it IS a long-lived token
        // so the only way to generate a new one, is to login again on the client side.
        // The user needs to do this.
        if (! $this->facebookService->testAccessToken($owner->getFacebookUserToken())) {
            Log::error('Deleting listing from facebook: User facebook token expired or invalid - '.$logString);
            $listing->status = Listings::STATUS_FAILED_DELETE;
            $listing->status_updated_at = Carbon::now();
            $listing->status_history = 'Facebook access token expired or invalid.';
            $listing->save();

            $command->delete();

            return;
        }

        // Loop over all the items and queue them for deletion.
        foreach($listing->items as $listingItem) {
            $job = new DeleteListingItemFromFacebookCommand($owner->id, $listing->id, $listingItem->id);
            $job->onConnection(QueueHelper::pickFacebookDeleter());
            $this->dispatch($job);
        }
    }
}
