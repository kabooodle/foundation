<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Shipping;

use Carbon\Carbon;
use Kabooodle\Bus\Events\Shipping\ShippingLabelPrinted;

/**
 * Class ShippingLabelPrintedEventHandler
 * @package Kabooodle\Bus\Handlers\Events\Shipping
 */
class ShippingLabelPrintedEventHandler
{
    /**
     * @param ShippingLabelPrinted $event
     */
    public function handle(ShippingLabelPrinted $event)
    {
        $shippingTransaction = $event->getShippingTransaction();

        if (! $shippingTransaction->isLabelPrinted()) {
            $shippingTransaction->shipping_status = 'LABEL PRINTED';
            $shippingTransaction->shipping_status_updated_on = Carbon::now();
            $shippingTransaction->save();
        }
    }
}
