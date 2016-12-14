<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Console\Commands;

use Bugsnag;
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

            Bugsnag::notifyError('enqueuer', 'we have listings', null, 'info');
            Bugsnag::leaveBreadcrumb('listings', \Bugsnag\Breadcrumbs\Breadcrumb::LOG_TYPE, $listings);

            // Build our job
            $job = $this->buildJob($listings);

            Bugsnag::notifyError('enqueuer', 'job was built', null, 'info');
            Bugsnag::leaveBreadcrumb('job built', \Bugsnag\Breadcrumbs\Breadcrumb::LOG_TYPE, $job);

            // Dispatch the listings queue handler for the listings.
            $this->dispatch($job);

            Bugsnag::notifyError('enqueuer', 'job was dispatched', null, 'info');

//            event(new ListingsWereQueued($job));

            $listingsIds = $listings->pluck('id')->toArray();

            // Update the Queues status to processing.
            $this->updateListingsStatus($listingsIds, $this->timestamp, Listings::STATUS_QUEUED_LIST);

            Bugsnag::notifyError('enqueuer', 'status was updated', null, 'info');
            Bugsnag::leaveBreadcrumb('listings', \Bugsnag\Breadcrumbs\Breadcrumb::LOG_TYPE, $listingsIds);
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
            'queue' => 'default',
//            'payload' => serialize($job),
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

    /**
     * @param array  $listingIds
     * @param Carbon $timestamp
     * @param string $status
     *
     * @return bool|int
     */
    public function updateListingsStatus(array $listingIds, Carbon $timestamp, string $status = Listings::STATUS_QUEUED_LIST)
    {
        return Listings::updateListingsStatus($listingIds, $timestamp, $status);
    }
}
