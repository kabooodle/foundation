<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Contracts;

/**
 * Interface ProductPricingInterface
 * @package Kabooodle\Models\Contracts
 */
interface ProductPricingInterface
{
    public function getAdjustedTotalAmount();
}
