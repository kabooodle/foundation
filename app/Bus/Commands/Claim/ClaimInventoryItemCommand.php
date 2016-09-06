<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Claim;

use Kabooodle\Models\User;
use Kabooodle\Models\Inventory;
use Kabooodle\Models\Contracts\ShoppableInterface;

/**
 * Class ClaimInventoryItemCommand
 * @package Kabooodle\Bus\Commands\Claim
 */
class ClaimInventoryItemCommand
{
    /**
     * ClaimInventoryItemCommand constructor.
     *
     * @param User               $claimedBy
     * @param ShoppableInterface $shoppable
     * @param Inventory          $inventoryItem
     */
    public function __construct(User $claimedBy, ShoppableInterface $shoppable, Inventory $inventoryItem)
    {
        $this->claimedBy = $claimedBy;
        $this->shoppable = $shoppable;
        $this->inventoryItem = $inventoryItem;
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
}