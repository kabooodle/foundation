<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Shipping;

use Kabooodle\Models\ShippingTransactions;
use Kabooodle\Models\ShippingTransactionHistory;

/**
 * Class ShippingTransactionStatusUpdatedEvent
 */
final class ShippingTransactionStatusUpdatedEvent
{
    /**
     * @var int
     */
    public $shippingTransactionId;

    /**
     * @var int
     */
    public $shippingTransactionHistoryId;

    /**
     * @param int $shippingTransactionId
     * @param int $shippingTransactionHistoryId
     */
    public function __construct(int $shippingTransactionId, int $shippingTransactionHistoryId)
    {
        $this->shippingTransactionId = $shippingTransactionId;
        $this->shippingTransactionHistoryId = $shippingTransactionHistoryId;
    }

    /**
     * @return int
     */
    public function getShippingTransactionId(): int
    {
        return $this->shippingTransactionId;
    }

    /**
     * @return int
     */
    public function getShippingTransactionHistoryId(): int
    {
        return $this->shippingTransactionHistoryId;
    }
}
