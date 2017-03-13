<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Flashsale;

use Kabooodle\Models\User;
use Kabooodle\Models\FlashSales;

/**
 * Class DeleteFlashsaleCommand
 */
final class DeleteFlashsaleCommand
{
    /**
     * @var FlashSales
     */
    public $flashsale;

    /**
     * @var User
     */
    public $user;

    /**
     * @param FlashSales $flashSale
     * @param User       $user
     */
    public function __construct(FlashSales $flashSale, User $user)
    {
        $this->flashsale = $flashSale;
        $this->user = $user;
    }

    /**
     * @return FlashSales
     */
    public function getFlashsale(): FlashSales
    {
        return $this->flashsale;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
