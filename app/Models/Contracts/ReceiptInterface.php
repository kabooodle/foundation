<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models\Contracts;

/**
 * Interface ProductPricingInterface
 * @package Kabooodle\Models\Contracts
 */
interface ReceiptInterface
{
    public function getUserEmail();
    public function receiptDate();
    public function receiptNumber();
    public function purchaseDate();
    public function discount();
    public function tax();
    public function totalAmount();
    public function previousBalance();
    public function description();
}