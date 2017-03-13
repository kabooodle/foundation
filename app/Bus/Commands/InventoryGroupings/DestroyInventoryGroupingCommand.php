<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\InventoryGroupings;

use Kabooodle\Models\InventoryGrouping;
use Kabooodle\Models\User;

/**
 * Class DestroyInventoryGroupingCommand
 * @package Kabooodle\Bus\Commands\InventoryGroupings
 */
final class DestroyInventoryGroupingCommand
{
    /**
     * @var User
     */
    protected $actor;

    /**
     * @var InventoryGrouping
     */
    protected $inventoryGrouping;

    /**
     * DestroyInventoryGroupingCommand constructor.
     *
     * @param User $actor
     * @param InventoryGrouping $inventoryGrouping
     */
    public function __construct(User $actor, InventoryGrouping $inventoryGrouping)
    {
        $this->actor = $actor;
        $this->inventoryGrouping = $inventoryGrouping;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
    }

    /**
     * @return InventoryGrouping
     */
    public function getInventoryGrouping(): InventoryGrouping
    {
        return $this->inventoryGrouping;
    }
}