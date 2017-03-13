<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Flashsale\Inventory;

use Kabooodle\Models\FlashSales;
use Kabooodle\Models\User;

/**
 * Class GetSellerInventoryCommand
 * @package Kabooodle\Bus\Commands\Flashsale\Inventory
 */
class GetSellerInventoryCommand
{
    /**
     * GetSellerInventoryCommand constructor.
     *
     * @param FlashSales $flashsale
     * @param User       $user
     */
    public function __construct(FlashSales $flashsale, User $actor)
    {
        $this->flashsale = $flashsale;
        $this->actor = $actor;
    }

    /**
     * @return FlashSales
     */
    public function getFlashsale()
    {
        return $this->flashsale;
    }

    /**
     * @return User
     */
    public function getActor()
    {
        return $this->actor;
    }
}
