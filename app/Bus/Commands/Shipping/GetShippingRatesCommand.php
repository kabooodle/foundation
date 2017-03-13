<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
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
     * @var array
     */
    public $claimIds;

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
     * @param array         $claimIds
     * @param MailingAddress $recipient
     * @param MailingAddress $sender
     * @param ParcelObject   $parcelObject
     */
    public function __construct(User $actor, array $claimIds, MailingAddress $recipient, MailingAddress $sender, ParcelObject $parcelObject)
    {
        $this->actor = $actor;
        $this->claimIds = $claimIds;
        $this->parcelObject = $parcelObject;
        $this->recipient = $recipient;
        $this->sender = $sender;
    }

    /**
     * @return ParcelObject
     */
    public function getParcel() : ParcelObject
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
     * @return array
     */
    public function getClaimIds()
    {
        return $this->claimIds;
    }
}