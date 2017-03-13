<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Console\Commands;

use Bugsnag;
use Exception;
use Carbon\Carbon;
use Kabooodle\Models\Queues;
use Kabooodle\Models\Listings;
use Illuminate\Console\Command;
use Kabooodle\Models\ListingItems;
use Kabooodle\Libraries\QueueHelper;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Services\Listings\ListingsService;
use Kabooodle\Bus\Jobs\Listings\Deletion\EnqueueScheduleListingsForDeletionJob;
use Kabooodle\Bus\Jobs\Listings\Deletion\EnqueueScheduleListingItemForDeletionJob;

/**
 * Class FacebookDeletionEnqueuerCommand
 */
class FacebookDeletionEnqueuerCommand extends Command
{
    use DispatchesJobs;

    /**
     * @var
     */
    protected $listingService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facebook:delete-enqueue';

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
        try {
            $this->timestamp = Carbon::now();

            $this->handleListings();

            $this->handleListingItems();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }
    }

    /**
     * Handles Listings that have been scheduled for deletion.
     */
    public function handleListings()
    {
        $listings = $this->getScheduledListings();
        $this->output->writeln($listings->count().' Listings found.');
        if ($listings && $listings->count() > 0) {
            $listingsIds = $listings->pluck('id')->toArray();
            // Update the Queues status to processing.
            Listings::updateListingsStatus(
                $listingsIds,
                $this->timestamp,
                Listings::STATUS_QUEUED_DELETE
            );

            $job = $this->buildListingsJob($listingsIds);

            $this->dispatch($job);
        }

        $this->output->writeln('Listings Completed');

        return;
    }

    public function handleListingItems()
    {
        $listingItems = $this->getScheduledListingItems();
        $this->output->writeln($listingItems->count().' Listing Items found.');

        if ($listingItems && $listingItems->count() > 0) {
            $listingItemIds = $listingItems->pluck('id')->toArray();
            // Update the Queues status to processing.
            ListingItems::updateListingsStatus(
                $listingItemIds,
                $this->timestamp,
                Listings::STATUS_QUEUED_DELETE
            );

            // Listing Items are queued one by one, not in a batch.
            foreach($listingItemIds as $listingItemId) {
                $job = $this->buildListingItemJob($listingItemId);
                $this->dispatch($job);
            }
        }

        $this->output->writeln('Listing Items Completed');

        return;
    }

    /**
     * @param array $listingIds
     *
     * @return EnqueueScheduleListingsForDeletionJob
     */
    public function buildListingsJob(array $listingIds)
    {
        $queueConnectionName = QueueHelper::pickFacebookSchedulerDelete();

        $job = new EnqueueScheduleListingsForDeletionJob($listingIds);
        $job->onConnection($queueConnectionName);

        // Store details about the job in the DB for our own personal records.
        $localQueueDb = Queues::create([
            'queue' => $queueConnectionName,
            'queue_group' => $queueConnectionName,
            'payload' => serialize($job),
            'status' => Queues::STATUS_QUEUED,
            'status_updated_at' => $this->timestamp
        ]);

        $job->setQueuesId($localQueueDb->id);

        return $job;
    }

    /**
     * @param int $listingItemId
     *
     * @return EnqueueScheduleListingItemForDeletionJob
     */
    public function buildListingItemJob(int $listingItemId)
    {
        $queueConnectionName = QueueHelper::pickFacebookSchedulerDelete();

        $job = new EnqueueScheduleListingItemForDeletionJob($listingItemId);
        $job->onConnection($queueConnectionName);

        // Store details about the job in the DB for our own personal records.
        $localQueueDb = Queues::create([
            'queue' => $queueConnectionName,
            'queue_group' => $queueConnectionName,
            'payload' => serialize($job),
            'status' => Queues::STATUS_QUEUED,
            'status_updated_at' => $this->timestamp
        ]);

        $job->setQueuesId($localQueueDb->id);

        return $job;
    }

    /**
     * @return mixed
     */
    public function getScheduledListingItems()
    {
        return $this->getListingServiceInstance()->getScheduledForDeletionListingItems();
    }

    /**
     * @return mixed
     */
    public function getScheduledListings()
    {
        return $this->getListingServiceInstance()->getScheduledForDeletionListings();
    }

    /**
     * @return ListingsService
     */
    public function getListingServiceInstance()
    {
        if (!$this->listingService) {
            $this->listingService = app()->make(ListingsService::class);
        }

        return $this->listingService;
    }
}
