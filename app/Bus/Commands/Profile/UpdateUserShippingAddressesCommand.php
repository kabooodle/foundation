<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Profile;

use Kabooodle\Models\User;
use Kabooodle\Models\MailingAddress;

/**
 * Class UpdateUserShippingAddressesCommand
 * @package Kabooodle\Commands\Profile
 */
class UpdateUserShippingAddressesCommand
{
    /**
     * UpdateUserShippingAddressesCommand constructor.
     *
     * @param User           $actor
     * @param MailingAddress $from
     * @param MailingAddress $to
     */
    public function __construct(User $actor, MailingAddress $from, MailingAddress $to)
    {
        $this->actor = $actor;
        $this->fromAddress = $from;
        $this->toAddress = $to;
    }

    /**
     * @return User
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * @return MailingAddress
     */
    public function getFromAddress()
    {
        return $this->fromAddress;
    }

    /**
     * @return MailingAddress
     */
    public function getToAddress()
    {
        return $this->toAddress;
    }
}