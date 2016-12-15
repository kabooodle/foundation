<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Console\Commands;

use Carbon\Carbon;
use Kabooodle\Models\Queues;
use Kabooodle\Models\Listings;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Jobs\EnqueueScheduleListingsJob;
use Kabooodle\Bus\Events\Listings\ListingsWereQueued;

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
     * @var string
     */
    public $timestamp;

    /**
     * @var string
     */
    public $queueConnectionGroupName = 'iron-facebook-scheduler';

    /**
     * This is step 1 of 3.
     *
     * Step 1 is FacebookEnqueuerCommand
     * Step 2 is EnqueueScheduleListingsJob
     * Step 3 is EnqueueScheduleListingItemJob
     */
    public function handle()
    {
        $this->timestamp = Carbon::now();

        $listings = $this->getScheduledListings();

        $this->output->writeln($listings->count().' Listings found.');
        if ($listings && $listings->count() > 0) {

            $job = $this->buildJob($listings);

            $this->dispatch($job);

            event(new ListingsWereQueued($job));

            $listingsIds = $listings->pluck('id')->toArray();

            // Update the Queues status to processing.
            Listings::updateListingsStatus($listingsIds, $this->timestamp, Listings::STATUS_QUEUED_LIST);
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
        $job->onConnection('iron-facebook-scheduler');

        // Store details about the job in the DB for our own personal records.
        $localQueueDb = Queues::create([
            'queue' => $this->queueConnectionGroupName,
            'queue_group' => $this->queueConnectionGroupName,
            'payload' => serialize($job),
            'status' => Queues::STATUS_QUEUED,
            'status_updated_at' => $this->timestamp,
        ]);

        $job->setQueuesId($localQueueDb->id);

        return $job;
    }

    /**
     * @return mixed
     */
    public function getScheduledListings()
    {
        $cachedNow = Carbon::now()->getTimestamp();

        // Set the start time to now
        $startTime = Carbon::createFromTimestamp($cachedNow);

        // Our endtime lookahead is 4 minutes, 59 seconds.
        $endTime = Carbon::createFromTimestamp($cachedNow)->addMinutes(4)->addSeconds(59);

        return Listings::getScheduledListings($startTime, $endTime);
    }
}
