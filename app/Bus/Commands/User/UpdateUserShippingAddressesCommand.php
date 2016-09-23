<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\User;

use Kabooodle\Models\User;
use Kabooodle\Models\MailingAddress;

/**
 * Class UpdateUserShippingAddressesCommand
 * @package Kabooodle\Commands\Profile
 */
final class UpdateUserShippingAddressesCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var MailingAddress
     */
    public $fromAddress;

    /**
     * @var MailingAddress
     */
    public $toAddress;

    /**
     * UpdateUserShippingAddressesCommand constructor.
     *
     * @param User           $actor
     * @param MailingAddress $fromAddress
     * @param MailingAddress $toAddress
     */
    public function __construct(User $actor, MailingAddress $fromAddress, MailingAddress $toAddress)
    {
        $this->actor = $actor;
        $this->fromAddress = $fromAddress;
        $this->toAddress = $toAddress;
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