<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Claim;

use Kabooodle\Models\Email;
use Kabooodle\Models\User;
use Kabooodle\Models\Inventory;
use Kabooodle\Models\Contracts\ShoppableInterface;

/**
 * Class ClaimInventoryItemCommand.
 */
class ClaimInventoryItemCommand
{
    protected $claimedBy;
    protected $shoppable;
    protected $inventoryItem;
    protected $guest;
    protected $email;

    /**
     * ClaimInventoryItemCommand constructor.
     * @param User $claimedBy
     * @param ShoppableInterface $shoppable
     * @param Inventory $inventoryItem
     * @param bool $guest
     * @param Email|null $email
     */
    public function __construct(
        User $claimedBy,
        ShoppableInterface $shoppable,
        Inventory $inventoryItem,
        $guest = false,
        Email $email = null)
    {
        $this->claimedBy = $claimedBy;
        $this->shoppable = $shoppable;
        $this->inventoryItem = $inventoryItem;
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
    public function getInventoryItem()
    {
        return $this->inventoryItem;
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
