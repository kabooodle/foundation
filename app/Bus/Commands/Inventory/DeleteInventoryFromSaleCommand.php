<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Inventory;

use Kabooodle\Models\User;

/**
 * Class DeleteInventoryFromSaleCommand
 * @package Kabooodle\Bus\Commands\Inventory
 */
class DeleteInventoryFromSaleCommand
{
    /**
     * DeleteInventoryFromSaleCommand constructor.
     *
     * @param User $user
     * @param      $flashSaleItemPivotId
     */
    public function __construct(User $user, $flashSaleItemPivotId)
    {
        $this->user = $user;
        $this->flashSaleItemPivotId = $flashSaleItemPivotId;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getflashSaleItemPivotId()
    {
        return $this->flashSaleItemPivotId;
    }
}
