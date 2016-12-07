<?php

namespace Kabooodle\Bus\Commands\Listings;

use Carbon\Carbon;

/**
 * Class GetScheduledListingsCommand
 */
final class GetScheduledListingsCommand
{
    /**
     * @var Carbon
     */
    public $startTime;

    /**
     * @var Carbon
     */
    public $endTime;

    /**
     * @param Carbon $startTime
     * @param Carbon $endTime
     */
    public function __construct(Carbon $startTime, Carbon $endTime)
    {
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }

    /**
     * @return Carbon
     */
    public function getStartTime(): Carbon
    {
        return $this->startTime;
    }

    /**
     * @return Carbon
     */
    public function getEndTime(): Carbon
    {
        return $this->endTime;
    }
}
