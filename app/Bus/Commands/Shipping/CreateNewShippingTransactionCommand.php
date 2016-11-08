<?php

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
    public $shipmentUUID;

    /**
     * CreateNewShippingTransactionCommand constructor.
     * @param User $actor
     * @param string $rateUUID
     * @param string $shipmentUUID
     */
    public function __construct(User $actor, string $rateUUID, string $shipmentUUID)
    {
        $this->actor = $actor;
        $this->rateUUID = $rateUUID;
        $this->shipmentUUID = $shipmentUUID;
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
    public function getShipmentUUID(): string
    {
        return $this->shipmentUUID;
    }
}