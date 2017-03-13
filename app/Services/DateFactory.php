<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Services;

use Carbon\Carbon;

/**
 * Class DateFactory
 */
class DateFactory
{
    /**
     * @var string
     */
    public $appTimezone;

    /**
     * @var string
     */
    public $userTimezone;

    /**
     * @param string $appTimezone
     * @param string $userTimezone
     */
    public function __construct(string $appTimezone, string $userTimezone)
    {
        $this->appTimezone = $appTimezone;
        $this->userTimezone = $userTimezone;
    }

    /**
     * @return Carbon
     */
    public function now()
    {
        return Carbon::now($this->userTimezone, $this->userTimezone);
    }

    /**
     * @param $dateTime
     *
     * @return Carbon
     */
    public function parse($dateTime)
    {
        return Carbon::parse($dateTime, $this->userTimezone);
    }
}
