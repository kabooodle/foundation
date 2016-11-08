<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Shipping;

use Kabooodle\Models\ShippingTransactions;

/**
 * Class RegisterShippoWebhookCommand
 * @package Kabooodle\Bus\Commands\Shipping
 */
final class RegisterShippoWebhookCommand
{
    /**
     * @var ShippingTransactions
     */
    public $shippingTransaction;

    /**
     * RegisterShippoWebhookCommand constructor.
     *
     * @param ShippingTransactions $shippingTransaction
     */
    public function __construct(ShippingTransactions $shippingTransaction)
    {
        $this->shippingTransaction = $shippingTransaction;
    }

    /**
     * @return ShippingTransactions
     */
    public function getShippingTransaction(): ShippingTransactions
    {
        return $this->shippingTransaction;
    }
}