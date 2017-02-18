<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Foundation\Exceptions\Listings;

use Exception;

/**
 * Class ListingExceedsHourlyLimitException
 */
class ListingExceedsHourlyLimitException extends Exception
{
    /**
     * @var
     */
    public $totalForHour;

    /**
     * @param $total
     *
     * @return $this
     */
    public function setTotalForHour($total)
    {
        $this->totalForHour = $total;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalForHour()
    {
        return $this->totalForHour;
    }
}
