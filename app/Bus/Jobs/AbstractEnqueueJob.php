<?php

namespace Kabooodle\Bus\Jobs;

use Carbon\Carbon;
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
    use DispatchesJobs, InteractsWithQueue, SerializesModels;

    /**
     * @param array $listingIds
     * @param Carbon $timestamp
     * @param string $status
     * @return bool|int
     */
    public function updateListingsStatus(array $listingIds, Carbon $timestamp, string $status = Listings::STATUS_QUEUED_LIST)
    {
        return Listings::whereIn('id', $listingIds)
            ->update([
                'status' => $status,
                'status_updated_at' => $timestamp->format('Y-m-d H:i:s')
            ]);
    }

    /**
     * @param array $listingIds
     * @param Carbon $timestamp
     * @param string $status
     * @return bool|int
     */
    public function updateListingItemsStatus(array $listingIds, Carbon $timestamp, string $status = Listings::STATUS_QUEUED_LIST)
    {
        return ListingItems::whereIn('id', $listingIds)
            ->update([
                'status' => $status,
                'status_updated_at' => $timestamp->format('Y-m-d H:i:s')
            ]);
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
     * @param string $status
     * @param $payload
     * @return static
     */
    public function createQueueStatus(string $queueName = 'default', string $status = Queues::STATUS_QUEUED, $payload)
    {
        return Queues::create([
                'queue' => $queueName,
                'payload' => serialize($payload),
                'status' => $status,
                'status_updated_at' => Carbon::now(),
            ]);
    }
}
