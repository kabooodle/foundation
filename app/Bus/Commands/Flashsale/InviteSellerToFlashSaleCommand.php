<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Flashsale;

use Kabooodle\Models\User;
use Kabooodle\Models\FlashSales;

/**
 * Class InviteSellerToFlashSaleCommand
 * @package Kabooodle\Bus\Commands\Flashsale
 */
class InviteSellerToFlashSaleCommand
{
    /**
     * InviteSellerToFlashSaleCommand constructor.
     *
     * @param FlashSales $flashsale
     * @param User       $invitedBy
     * @param            $email
     */
    public function __construct(FlashSales $flashsale, User $invitedBy, $email)
    {
        $this->flashsale = $flashsale;
        $this->invitedBy = $invitedBy;
        $this->email = $email;
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
    public function getInvitedBy()
    {
        return $this->invitedBy;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }
}
