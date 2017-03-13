<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Claim;

use Kabooodle\Models\Contracts\Claimable;
use Kabooodle\Models\Contracts\ListableInterface;
use Kabooodle\Models\Email;
use Kabooodle\Models\User;

/**
 * Class ClaimListedItemCommand.
 */
class ClaimListedItemCommand
{
    protected $claimedBy;
    protected $listingItem;
    protected $listedItem;
    protected $guest;
    protected $email;

    /**
     * ClaimListedItemCommand constructor.
     * @param User $claimedBy
     * @param Claimable $listingItem
     * @param ListableInterface $listedItem
     * @param bool $guest
     * @param Email|null $email
     */
    public function __construct(
        User $claimedBy,
        Claimable $listingItem,
        ListableInterface $listedItem,
        $guest = false,
        Email $email = null)
    {
        $this->claimedBy = $claimedBy;
        $this->listingItem = $listingItem;
        $this->listedItem = $listedItem;
        $this->guest = $guest;
        $this->email = $email;
    }

    /**
     * @return User
     */
    public function getClaimedBy()
    {
        return $this->claimedBy;
    }

    /**
     * @return ListableInterface
     */
    public function getListedItem()
    {
        return $this->listedItem;
    }

    /**
     * @return Claimable
     */
    public function getListingItem()
    {
        return $this->listingItem;
    }

    /**
     * @return boolean
     */
    public function isGuest(): bool
    {
        return $this->guest;
    }

    /**
     * @return Email|null
     */
    public function getEmail()
    {
        return $this->email;
    }
}
