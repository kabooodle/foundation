<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Listing;

use Carbon\Carbon;
use Kabooodle\Services\DateFactory;
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
     * @var DateFactory
     */
    public $dateFactory;

    /**
     * @param string|null $startsAt
     * @param string|null $endsAt
     * @param string|null $claimingStartsAt
     * @param string|null $claimingEndsAt
     * @param string|null $itemMessage
     */
    public function __construct(string $startsAt = null, string $endsAt = null, string $claimingStartsAt = null, string $claimingEndsAt = null, string $itemMessage = null)
    {
        $this->dateFactory = app()->make(DateFactory::class);
        $this->setStartsAt($startsAt ?: $this->dateFactory->now()->addSeconds(ListingsService::SCHEDULER_LOOKAHEAD_FROM_NOW_SECONDS)->toDateTimeString());
        $this->setEndsAt($endsAt ?: $this->dateFactory->now()->addHours(168)->toDateTimeString()); // 7 days
        $this->setClaimingStartsAt($claimingStartsAt);
        $this->setClaimingEndsAt($claimingEndsAt);
        $this->setItemMessage($itemMessage);
    }

    /**
     * @param string|null $startsAt
     */
    public function setStartsAt($startsAt)
    {
        $this->startsAt = $this->dateFactory->parse($startsAt);
    }

    /**
     * @param string|null $endsAt
     */
    public function setEndsAt($endsAt)
    {
        $this->endsAt = $this->dateFactory->parse($endsAt);
    }

    /**
     * @param string|null $claimingStartsAt
     */
    public function setClaimingStartsAt($claimingStartsAt)
    {
        if ($claimingStartsAt) {
            $this->claimingStartsAt = $this->dateFactory->parse($claimingStartsAt);
        }
    }

    /**
     * @param string|null $claimingEndsAt
     */
    public function setClaimingEndsAt($claimingEndsAt)
    {
        if ($claimingEndsAt) {
            $this->claimingEndsAt = $this->dateFactory->parse($claimingEndsAt);
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
