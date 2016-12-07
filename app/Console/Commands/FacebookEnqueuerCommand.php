<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Console\Commands;

use Carbon\Carbon;
use Kabooodle\Models\Queues;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
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
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facebook:enqueue';

    /**
     * @return void
     */
    public function handle()
    {
        // Cached timestamp of now so we can use it in multiple places.
        $cachedNow = Carbon::now()->getTimestamp();

        // Set the start time to now
        $startTime = Carbon::createFromTimestamp($cachedNow);

        // Our endtime lookahead is 4 minutes, 59 seconds.
        $endTime = Carbon::createFromTimestamp($cachedNow)->addMinutes(4)->addSeconds(59);

        // Get all the listings
        $listings = $this->dispatchNow(new GetScheduledListingsCommand($startTime, $endTime));

        $this->output->writeln($listings->count().' Listings found.');

        if ($listings && $listings->count() > 0) {

            // Build our job
            $job = $this->buildJob($listings);

            // Dispatch the listings queue handler for the listings.
            $this->dispatch($job);
        }

        $this->output->writeln('Completed');

        return;
    }

    /**
     * @param Collection $listings
     *
     * @return EnqueueScheduleListingsJob
     */
    public function buildJob(Collection $listings)
    {
        $job = new EnqueueScheduleListingsJob($listings);

        // Store details about the job in the DB for our own personal records.
        $localQueueDb = Queues::create([
            'queue' => 'default',
            'payload' => serialize($job),
            'status' => Queues::STATUS_QUEUED,
            'status_updated_at' => Carbon::now(),
        ]);

        $job->setQueuesId($localQueueDb->id);

        return $job;
    }
}
