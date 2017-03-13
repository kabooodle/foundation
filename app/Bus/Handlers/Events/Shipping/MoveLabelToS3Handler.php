<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Shipping;

use Illuminate\Contracts\Queue\ShouldQueue;
use Kabooodle\Bus\Events\Shipping\ShippingTransactionWasCreatedEvent;

/**
 * Class MoveLabelToS3Handler
 */
class MoveLabelToS3Handler implements ShouldQueue
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
