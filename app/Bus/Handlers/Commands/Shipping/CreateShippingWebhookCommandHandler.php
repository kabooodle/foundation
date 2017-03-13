<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Shipping;

use Kabooodle\Models\ShippingWebhooks;
use Kabooodle\Bus\Commands\Shipping\CreateShippingWebhookCommand;

/**
 * Class CreateShippingWebhookCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Shipping
 */
class CreateShippingWebhookCommandHandler
{
    /**
     * @param CreateShippingWebhookCommand $command
     */
    public function handle(CreateShippingWebhookCommand $command)
    {
        $transaction = $command->getShippingTransaction();
        $data = $command->getShippoResponse();

        ShippingWebhooks::create([
            'shipping_transaction_id' => $transaction->id,
            'data' => $data
        ]);
    }
}
