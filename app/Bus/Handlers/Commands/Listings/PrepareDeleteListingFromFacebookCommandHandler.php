<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Listings;

use Carbon\Carbon;
use Kabooodle\Models\Listings;
use Kabooodle\Libraries\QueueHelper;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Listings\DeleteListingFromFacebookCommand;
use Kabooodle\Bus\Commands\Listings\PrepareDeleteListingFromFacebookCommand;

/**
 * Class PrepareDeleteListingFromFacebookCommandHandler
 */
class PrepareDeleteListingFromFacebookCommandHandler
{
    use DispatchesJobs;

    /**
     * @param PrepareDeleteListingFromFacebookCommand $command
     *
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function handle(PrepareDeleteListingFromFacebookCommand $command)
    {
        $listing = Listings::where('id', '=', $command->getListingId())
            ->where('owner_id', '=', $command->getActor()->id)
            ->firstOrFail();

        $listing->status = Listings::STATUS_QUEUED_DELETE;
        $listing->status_updated_at = Carbon::now();
        $listing->save();

        $job = new DeleteListingFromFacebookCommand($command->getActor()->id, $command->getListingId());
        $job->onConnection(QueueHelper::pickFacebookDeleter());
        $this->dispatch($job);

        return $listing;
    }
}
