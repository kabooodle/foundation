<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Shipping;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kabooodle\Services\Shippr\ShipprService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Shipping\CreateShippingWebhookCommand;
use Kabooodle\Bus\Events\Shipping\ShippingTransactionWasCreatedEvent;

/**
 * Class DispatchShippingWebhookHandler
 * @package Kabooodle\Bus\Handlers\Events\Shipping
 */
class DispatchShippingWebhookHandler implements ShouldQueue
{
    use DispatchesJobs, InteractsWithQueue, SerializesModels;

        /**
     * @var ShipprService
     */
    public $shipprService;

    /**
     * DispatchShippingWebhookHandler constructor.
     *
     * @param ShipprService $shipprService
     */
    public function __construct(ShipprService $shipprService)
    {
        $this->shipprService = $shipprService;
    }

    /**
     * @param ShippingTransactionWasCreatedEvent $event
     *
     * @return void
     */
    public function handle(ShippingTransactionWasCreatedEvent $event)
    {
        $shippingTransaction = $event->getShippingTransaction();

        // TODO: Make carrier argument a variable
        // Register a Shipping Notifications Webhook with the shipping provider.
        // This will cause the shipping provider to make post requests to our system anytime the shipping
        // status for this item is updated.
        $response = $this->shipprService->registerTrackingWebhook('usps', $shippingTransaction->tracking_number);

        $this->dispatchNow(new CreateShippingWebhookCommand($shippingTransaction, $response));
    }
}
