<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Kabooodle\Models\Queues;
use Kabooodle\Models\Listings;
use Kabooodle\Models\ListingItems;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Class AbstractEnqueueJob
 */
abstract class AbstractEnqueueJob extends Job
{
    use DispatchesJobs, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param array $listingIds
     * @param Carbon $timestamp
     * @param string $status
     * @return bool|int
     */
    public function updateListingsStatus(array $listingIds, Carbon $timestamp, string $status)
    {
        return Listings::updateListingsStatus($listingIds, $timestamp, $status);
    }

    /**
     * @param array  $listingIds
     * @param Carbon $timestamp
     * @param string $status
     * @param array  $attributes
     *
     * @return bool|int
     */
    public function updateListingItemsStatus(array $listingIds, Carbon $timestamp, string $status, array $attributes = [])
    {
        return ListingItems::updateListingItemsStatus($listingIds, $timestamp, $status, $attributes);
    }

    /**
     * @param $queueId
     * @param Carbon $timestamp
     * @param string $status
     * @param int $attempts
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function updateQueueStatus(int $queueId, Carbon $timestamp, string $status = Queues::STATUS_PROCESSING, int $attempts = 0)
    {
        return Queues::where('id', '=', $queueId)
            ->update([
                'status' => $status,
                'status_updated_at' => $timestamp,
                'attempts' => $attempts
            ]);
    }

    /**
     * @param string $queueName
     * @param string $queueGroupName
     * @param string $status
     * @param        $payload
     *
     * @return static
     */
    public function createQueueStatus(string $queueName = 'default', string $queueGroupName = '', string $status = Queues::STATUS_QUEUED, $payload)
    {
        return Queues::create([
                'queue' => $queueName,
                'queue_group' => $queueGroupName,
                'payload' => serialize($payload),
                'status' => $status,
                'status_updated_at' => Carbon::now(),
            ]);
    }
}
