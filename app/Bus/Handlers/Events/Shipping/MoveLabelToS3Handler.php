<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Shipping;

use Kabooodle\Bus\Events\Shipping\ShippingTransactionWasCreatedEvent;

/**
 * Class MoveLabelToS3Handler
 * @package Kabooodle\Bus\Handlers\Events\Shipping
 */
class MoveLabelToS3Handler
{
    /**
     * @param ShippingTransactionWasCreatedEvent $event
     */
    public function handle(ShippingTransactionWasCreatedEvent $event)
    {
        // TODO: Stream file from remote location to AWS S3.
//        $transaction->label_file_embedded = file_get_contents($transaction->label_url);
//        $transaction->save();
    }
}
