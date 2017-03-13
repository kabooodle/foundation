<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Shipping;

use Kabooodle\Models\User;

/**
 * Class GetShippingTransactionCommand
 * @package Kabooodle\Bus\Commands\Shipping
 */
final class GetShippingTransactionCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var string
     */
    public $shippingShipmentUUID;

    /**
     * @var string
     */
    public $transactionUUID;

    /**
     * GetShippingTransactionCommand constructor.
     * @param User $actor
     * @param string $shippingShipmentUUID
     * @param string $transactionUUID
     */
    public function __construct(User $actor, string $shippingShipmentUUID, string $transactionUUID)
    {
        $this->actor = $actor;
        $this->shippingShipmentUUID = $shippingShipmentUUID;
        $this->transactionUUID = $transactionUUID;
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
    public function getShippingShipmentUUID(): string
    {
        return $this->shippingShipmentUUID;
    }

    /**
     * @return string
     */
    public function getTransactionUUID(): string
    {
        return $this->transactionUUID;
    }
}