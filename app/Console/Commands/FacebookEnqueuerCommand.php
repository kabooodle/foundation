<?php

namespace Kabooodle\Console\Commands;

use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Jobs\EnqueueScheduleListingsJob;
use Kabooodle\Bus\Commands\Listings\GetScheduledListingsCommand;

/**
 * Class FacebookEnqueuerCommand
 */
class FacebookEnqueuerCommand extends Command
{
    use DispatchesJobs;

    /**
     * @return void
     */
    public function handle()
    {
        // Cached timestamp of now;
        $timestamp = Carbon::now();

        // Our lookahead is 4 minutes, 59 seconds.
        $timestamp5MinutesFromNow = $timestamp->addMinutes(4)->addSeconds(59);

        // Get all the listings
        $listings = $this->dispatchNow(new GetScheduledListingsCommand($timestamp, $timestamp5MinutesFromNow));

        if ($listings && $listings->count() > 0) {

            // Dispatch the listings queue handler for the listings.
            $this->dispatch(new EnqueueScheduleListingsJob($listings));
        }

        $this->output->writeln();

        return;
    }
}
