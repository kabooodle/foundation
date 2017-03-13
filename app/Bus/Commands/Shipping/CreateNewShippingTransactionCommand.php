<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Shipping;

use Kabooodle\Models\User;

/**
 * Class CreateNewShippingTransactionCommand
 * @package Kabooodle\Bus\Commands\Shipping
 */
final class CreateNewShippingTransactionCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var string
     */
    public $rateUUID;

    /**
     * @var string
     */
    public $parcelId;

    /**
     * @param User   $actor
     * @param string $rateUUID
     * @param string $parcelId
     */
    public function __construct(User $actor, string $rateUUID, string $parcelId)
    {
        $this->actor = $actor;
        $this->rateUUID = $rateUUID;
        $this->parcelId = $parcelId;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
    }

    /**
     * @return string
     */
    public function getRateUUID(): string
    {
        return $this->rateUUID;
    }

    /**
     * @return string
     */
    public function getParcelId(): string
    {
        return $this->parcelId;
    }
}
