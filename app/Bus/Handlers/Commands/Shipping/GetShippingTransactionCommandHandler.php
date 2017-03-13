<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Shipping;

use Kabooodle\Models\ShippingTransactions;
use Kabooodle\Bus\Commands\Shipping\GetShippingTransactionCommand;

/**
 * Class GetShippingTransactionCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Shipping
 */
class GetShippingTransactionCommandHandler
{
    /**
     * @param GetShippingTransactionCommand $command
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function handle(GetShippingTransactionCommand $command)
    {
        $user = $command->getActor();
        $transactionUUID = $command->getTransactionUUID();
        $shippingShipmentUUID = $command->getShippingShipmentUUID();

        return ShippingTransactions::where('uuid', '=', $transactionUUID)
            ->where('shipping_shipments_uuid', '=', $shippingShipmentUUID)
            ->where('user_id', '=', $user->id)
            ->firstOrFail();
    }
}
