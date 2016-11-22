<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\User;

use Kabooodle\Models\User;
use Kabooodle\Models\MailingAddress;

/**
 * Class UpdateUserShippingProfileCommand
 */
final class UpdateUserShippingProfileCommand
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
     * @var bool;
     */
    public $kabooodleAsDefaultshippingProvider;

    /**
     * @param User           $actor
     * @param MailingAddress $fromAddress
     * @param MailingAddress $toAddress
     * @param bool           $kabooodleAsDefaultshippingProvider
     */
    public function __construct(User $actor, MailingAddress $fromAddress, MailingAddress $toAddress, bool $kabooodleAsDefaultshippingProvider = true)
    {
        $this->actor = $actor;
        $this->fromAddress = $fromAddress;
        $this->toAddress = $toAddress;
        $this->kabooodleAsDefaultshippingProvider = $kabooodleAsDefaultshippingProvider;
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

    /**
     * @return bool
     */
    public function isKabooodleDefaultShippingProvider(): bool
    {
        return $this->kabooodleAsDefaultshippingProvider;
    }
}
