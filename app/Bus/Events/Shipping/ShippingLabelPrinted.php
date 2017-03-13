<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Shipping;

use Kabooodle\Models\ShippingTransactions;

/**
 * Class ShippingLabelPrinted
 * @package Kabooodle\Bus\Events\Shipping
 */
final class ShippingLabelPrinted
{
    /**
     * @var ShippingTransactions
     */
    public $shippingTransaction;

    /**
     * ShippingLabelPrinted constructor.
     * @param ShippingTransactions $shippingTransaction
     */
    public function __construct(ShippingTransactions $shippingTransaction)
    {
        $this->shippingTransaction = $shippingTransaction;
    }

    /**
     * @return ShippingTransactions
     */
    public function getShippingTransaction()
    {
        return $this->shippingTransaction;
    }
}
