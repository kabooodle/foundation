<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Shipping;

use Kabooodle\Models\User;
use Kabooodle\Models\MailingAddress;
use Kabooodle\Services\Shippr\ParcelObject;

/**
 * Class GetShippingRatesCommand
 * @package Kabooodle\Bus\Commands\Shipping
 */
final class GetShippingRatesCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var string
     */
    public $claimUUID;

    /**
     * @var MailingAddress
     */
    public $recipient;

    /**
     * @var MailingAddress
     */
    public $sender;

    /**
     * @var ParcelObject
     */
    public $parcelObject;

    /**
     * GetShippingRatesCommand constructor.
     *
     * @param User           $actor
     * @param string         $claimUUID
     * @param MailingAddress $recipient
     * @param MailingAddress $sender
     * @param ParcelObject   $parcelObject
     */
    public function __construct(User $actor, $claimUUID, MailingAddress $recipient, MailingAddress $sender, ParcelObject $parcelObject)
    {
        $this->actor = $actor;
        $this->claimUUID = $claimUUID;
        $this->parcelObject = $parcelObject;
        $this->recipient = $recipient;
        $this->sender = $sender;
    }

    /**
     * @return ParcelObject
     */
    public function getParcel()
    {
        return $this->parcelObject;
    }

    /**
     * @return MailingAddress
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @return MailingAddress
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @return User
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * @return mixed
     */
    public function getClaimUUID()
    {
        return $this->claimUUID;
    }
}