<?php

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

        if(! $shippingTransaction->isFulfilled()) {
            $shippingTransaction->fulfilled = true;
            $shippingTransaction->fulfilled_on = Carbon::now();
            $shippingTransaction->save();
        }
    }
}