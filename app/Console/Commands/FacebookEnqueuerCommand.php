<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Console\Commands;

use Bugsnag;
use Carbon\Carbon;
use Kabooodle\Models\Queues;
use Kabooodle\Models\Listings;
use Illuminate\Console\Command;
use Kabooodle\Libraries\QueueHelper;
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
        try {
            $this->timestamp = Carbon::now();

            $listings = $this->getScheduledListings();
            $listingsIds = $listings->pluck('id')->toArray();
            $this->output->writeln($listings->count().' Listings found.');
            if ($listings && $listings->count() > 0) {

                // Update the Queues status to processing.
                Listings::updateListingsStatus($listingsIds, $this->timestamp, Listings::STATUS_QUEUED_LIST);

                $job = $this->buildJob($listingsIds);

                $this->dispatch($job);

                event(new ListingsWereQueued($listings, $job));
            }

            $this->output->writeln('Completed');

            return;
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }
    }

    /**
     * @param array $listingsIds
     *
     * @return EnqueueScheduleListingsJob
     */
    public function buildJob(array $listingsIds)
    {
        $queueConnectionName = QueueHelper::pickFacebookScheduler();

        $job = new EnqueueScheduleListingsJob($listingsIds);
        $job->onConnection($queueConnectionName);

        // Store details about the job in the DB for our own personal records.
        $localQueueDb = Queues::create([
            'queue' => $queueConnectionName,
            'queue_group' => $queueConnectionName,
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
