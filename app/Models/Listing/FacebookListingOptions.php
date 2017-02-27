<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models\Listing;

use Carbon\Carbon;
use Kabooodle\Services\Listings\ListingsService;

/**
 * Class FacebookListingOptions
 */
final class FacebookListingOptions
{
    public $startsAt;
    public $endsAt;
    public $claimingStartsAt;
    public $claimingEndsAt;
    public $itemMessage;

    /**
     * @param string|null $startsAt
     * @param string|null $endsAt
     * @param string|null $claimingStartsAt
     * @param string|null $claimingEndsAt
     * @param string|null $itemMessage
     */
    public function __construct(string $startsAt = null, string $endsAt = null, string $claimingStartsAt = null, string $claimingEndsAt = null, string $itemMessage = null)
    {
        \Log::info('first '. $startsAt);
        $this->setStartsAt($startsAt ?: Carbon::now(current_timezone())->addSeconds(ListingsService::SCHEDULER_LOOKAHEAD_FROM_NOW_SECONDS)->toDateTimeString());
        $this->setEndsAt($endsAt ?: Carbon::now(current_timezone())->addHours(168)->toDateTimeString()); // 7 days
        $this->setClaimingStartsAt($claimingStartsAt);
        $this->setClaimingEndsAt($claimingEndsAt);
        $this->setItemMessage($itemMessage);
    }

    /**
     * @param string|null $startsAt
     */
    public function setStartsAt($startsAt)
    {
        \Log::info(strtotime($startsAt));
        \Log::info('starts at raw '.$startsAt);
        $this->startsAt = Carbon::createFromTimestamp(strtotime($startsAt), current_timezone());
        \Log::info('starts at after '.print_r($this->startsAt, true));
    }

    /**
     * @param string|null $endsAt
     */
    public function setEndsAt($endsAt)
    {
        $this->endsAt = Carbon::createFromTimestamp(strtotime($endsAt.' UTC'))->setTimezone('UTC');
    }

    /**
     * @param string|null $claimingStartsAt
     */
    public function setClaimingStartsAt($claimingStartsAt)
    {
        if ($claimingStartsAt) {
            $this->claimingStartsAt = Carbon::createFromTimestamp(strtotime($claimingStartsAt.' UTC'))->setTimezone('UTC');
        }
    }

    /**
     * @param string|null $claimingEndsAt
     */
    public function setClaimingEndsAt($claimingEndsAt)
    {
        if ($claimingEndsAt) {
            $this->claimingEndsAt = Carbon::createFromTimestamp(strtotime($claimingEndsAt.' UTC'))->setTimezone('UTC');
        }
    }

    /**
     * @param string|null $itemMessage
     */
    public function setItemMessage(string $itemMessage = null)
    {
        $this->itemMessage = $itemMessage;
    }

    /**
     * @return null|Carbon
     */
    public function getClaimingEndsAt()
    {
        return $this->claimingEndsAt;
    }

    /**
     * @return null|Carbon
     */
    public function getClaimingStartsAt()
    {
        return $this->claimingStartsAt;
    }

    /**
     * @return null|Carbon
     */
    public function getStartsAt()
    {
        return $this->startsAt;
    }

    /**
     * @return null|Carbon
     */
    public function getEndsAt()
    {
        return $this->endsAt;
    }

    /**
     * @return string|null
     */
    public function getItemMessage()
    {
        return $this->itemMessage;
    }
}
