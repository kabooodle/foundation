<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Shipping;

use Shippo_Object;
use Kabooodle\Models\ShippingTransactions;

/**
 * Class CreateShippingWebhookCommand
 * @package Kabooodle\Bus\Commands\Shipping
 */
final class CreateShippingWebhookCommand
{
    /**
     * @var ShippingTransactions
     */
    public $shippingTransaction;

    /**
     * @var Shippo_Object
     */
    public $shippoResponse;

    /**
     * CreateShippingWebhookCommand constructor.
     *
     * @param ShippingTransactions $shippingTransaction
     * @param Shippo_Object        $shippoResponse
     */
    public function __construct(ShippingTransactions $shippingTransaction, Shippo_Object $shippoResponse)
    {
        $this->shippingTransaction = $shippingTransaction;
        $this->shippoResponse = $shippoResponse;
    }

    /**
     * @return ShippingTransactions
     */
    public function getShippingTransaction(): ShippingTransactions
    {
        return $this->shippingTransaction;
    }

    /**
     * @return Shippo_Object
     */
    public function getShippoResponse(): Shippo_Object
    {
        return $this->shippoResponse;
    }
}