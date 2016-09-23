<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\User;

use Kabooodle\Models\ShippingAddress;
use Kabooodle\Bus\Commands\User\UpdateUserShippingAddressesCommand;

/**
 * Class UpdateUserShippingAddressesCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Profile
 */
class UpdateUserShippingAddressesCommandHandler
{
    /**
     * TODO: Abstract the 2 address types for better testing in the future.
     *
     * @param UpdateUserShippingAddressesCommand $command
     */
    public function handle(UpdateUserShippingAddressesCommand $command)
    {
        $actor = $command->getActor();
        $formShipFrom = $command->getFromAddress();
        $formToFrom = $command->getToAddress();

        $shipFromAddress = $actor->shipFromAddress ? : new ShippingAddress;
        $shipFromAddress->user_id = $actor->id;
        $shipFromAddress->type = ShippingAddress::TYPE_FROM;
        $shipFromAddress->street1 = $formShipFrom->getStreet1();
        $shipFromAddress->street2 = $formShipFrom->getStreet2();
        $shipFromAddress->city = $formShipFrom->getCity();
        $shipFromAddress->state = $formShipFrom->getState();
        $shipFromAddress->zip = $formShipFrom->getZip();
        $shipFromAddress->save();

        $shipToAddress = $actor->shipToAddress ? : new ShippingAddress;
        $shipToAddress->user_id = $actor->id;
        $shipToAddress->type = ShippingAddress::TYPE_TO;
        $shipToAddress->street1 = $formToFrom->getStreet1();
        $shipToAddress->street2 = $formToFrom->getStreet2();
        $shipToAddress->city = $formToFrom->getCity();
        $shipToAddress->state = $formToFrom->getState();
        $shipToAddress->zip = $formToFrom->getZip();
        $shipToAddress->save();
    }
}