<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models\Listing;

use Carbon\Carbon;

/**
 * Class FacebookListingOptions
 */
final class FacebookListingOptions
{
    public $startsAt;
    public $endsAt;
    public $claimingStartsAt;
    public $claimingEndsAt;
    public $includeLink;

    /**
     * @param string|null $startsAt
     * @param string|null $endsAt
     * @param string|null $claimingStartsAt
     * @param string|null $claimingEndsAt
     * @param bool        $includeLink
     */
    public function __construct(string $startsAt = null, string $endsAt = null, string $claimingStartsAt = null, string $claimingEndsAt = null, bool $includeLink = true)
    {
        $this->setStartsAt($startsAt);
        $this->setEndsAt($endsAt);
        $this->setClaimingStartsAt($claimingStartsAt);
        $this->setClaimingEndsAt($claimingEndsAt);
        $this->setIncludeLink($includeLink);
    }

    /**
     * @param string|null $startsAt
     */
    public function setStartsAt($startsAt)
    {
        if ($startsAt) {
            $this->startsAt = Carbon::createFromTimestamp(strtotime($startsAt));
        }
    }

    /**
     * @param string|null $endsAt
     */
    public function setEndsAt($endsAt)
    {
        if ($endsAt) {
            $this->endsAt = Carbon::createFromTimestamp(strtotime($endsAt));
        }
    }

    /**
     * @param string|null $claimingStartsAt
     */
    public function setClaimingStartsAt($claimingStartsAt)
    {
        if ($claimingStartsAt) {
            $this->claimingStartsAt = Carbon::createFromTimestamp(strtotime($claimingStartsAt));
        }
    }

    /**
     * @param string|null $claimingEndsAt
     */
    public function setClaimingEndsAt($claimingEndsAt)
    {
        if ($claimingEndsAt) {
            $this->claimingEndsAt = Carbon::createFromTimestamp(strtotime($claimingEndsAt));
        }
    }

    /**
     * @param boolean $includeLink
     */
    public function setIncludeLink(bool $includeLink)
    {
        $this->includeLink = $includeLink;
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
     * @return boolean
     */
    public function getIncludeLink(): bool
    {
        return $this->includeLink;
    }
}
