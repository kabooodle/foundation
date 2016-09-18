<?php

namespace Kabooodle\Bus\Handlers\Events\Shipping;

use Kabooodle\Bus\Events\Shipping\ShippingTransactionWasCreatedEvent;

/**
 * Class ShippingTransactionWasCreatedEventHandler
 * @package Kabooodle\Bus\Handlers\Events\Shipping
 */
class ShippingTransactionWasCreatedEventHandler
{
    public function __construct()
    {

    }

    /**
     * @param ShippingTransactionWasCreatedEvent $event
     */
    public function handle(ShippingTransactionWasCreatedEvent $event)
    {
        $transaction = $event->getTransaction();

        // TODO: Stream file from remote location to AWS S3.
//        $transaction->label_file_embedded = file_get_contents($transaction->label_url);
//        $transaction->save();
    }
}