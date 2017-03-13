<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Foundation\Exceptions\Listings;

use Exception;

/**
 * Class ListingExceedsHourlyLimitException
 */
class ListingExceedsHourlyLimitException extends Exception
{
    /**
     * @var int
     */
    public $totalExistingListings;

    /**
     * @var int
     */
    public $maximumTotalAllowedForHour;

    /**
     * @param int $totalExistingListings
     *
     * @return $this
     */
    public function setTotalExistingListings(int $totalExistingListings)
    {
        $this->totalExistingListings = $totalExistingListings;

        return $this;
    }

    /**
     * @param int $maximumTotalAllowedForHour
     *
     * @return $this
     */
    public function setMaximumTotalAllowedForHour(int $maximumTotalAllowedForHour)
    {
        $this->maximumTotalAllowedForHour = $maximumTotalAllowedForHour;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalExistingListingsForHour()
    {
        return $this->totalExistingListings;
    }

    /**
     * @return int
     */
    public function getMaximumTotalAllowedForHour()
    {
        return $this->maximumTotalAllowedForHour;
    }

    /**
     * @return int
     */
    public function getRemainingAllowedForHour()
    {
        return (int) ($this->getMaximumTotalAllowedForHour() - $this->getTotalExistingListingsForHour());
    }
}
