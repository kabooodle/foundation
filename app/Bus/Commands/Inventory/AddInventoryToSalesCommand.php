<?php

namespace Kabooodle\Bus\Commands\Inventory;

use Kabooodle\Models\User;

/**
 * Class AddInventoryToSalesCommand
 * @package Kabooodle\Bus\Commands\Inventory
 */
class AddInventoryToSalesCommand
{
    /**
     * AddInventoryToSalesCommand constructor.
     *
     * @param User  $user
     * @param array $inventoryIds
     * @param array $flashSalesIds
     */
    public function __construct(User $user, array $inventoryIds, array $flashSalesIds = [])
    {
        $this->user = $user;
        $this->inventoryIds = $inventoryIds;
        $this->flashSalesIds = $flashSalesIds;
    }

    /**
     * @return array
     */
    public function getFlashSalesIds()
    {
        return $this->flashSalesIds;
    }

    /**
     * @return array
     */
    public function getInventoryIds()
    {
        return $this->inventoryIds;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}