<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Claim;

use Kabooodle\Models\Email;
use Kabooodle\Models\User;
use Kabooodle\Models\Inventory;
use Kabooodle\Models\Contracts\ShoppableInterface;

/**
 * Class ClaimListedItemCommand.
 */
class ClaimListedItemCommand
{
    protected $claimedBy;
    protected $shoppable;
    protected $listedItem;
    protected $guest;
    protected $email;

    /**
     * ClaimListedItemCommand constructor.
     * @param User $claimedBy
     * @param ShoppableInterface $shoppable
     * @param Inventory $listedItem
     * @param bool $guest
     * @param Email|null $email
     */
    public function __construct(
        User $claimedBy,
        ShoppableInterface $shoppable,
        Inventory $listedItem,
        $guest = false,
        Email $email = null)
    {
        $this->claimedBy = $claimedBy;
        $this->shoppable = $shoppable;
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
     * @return Inventory
     */
    public function getListedItem()
    {
        return $this->listedItem;
    }

    /**
     * @return ShoppableInterface
     */
    public function getShoppable()
    {
        return $this->shoppable;
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
