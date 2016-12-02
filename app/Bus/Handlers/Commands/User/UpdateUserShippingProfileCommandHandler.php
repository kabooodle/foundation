<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\User;

use DB;
use Kabooodle\Models\ShippingAddress;
use Kabooodle\Bus\Commands\User\UpdateUserShippingProfileCommand;

/**
 * Class UpdateUserShippingProfileCommandHandler
 */
class UpdateUserShippingProfileCommandHandler
{
    /**
     * @param UpdateUserShippingProfileCommand $command
     *
     * @return mixed
     */
    public function handle(UpdateUserShippingProfileCommand $command)
    {
        $actor = $command->getActor();
        $formShipFrom = $command->getFromAddress();
        $formToFrom = $command->getToAddress();

        return DB::transaction(function () use ($command, $actor, $formShipFrom, $formToFrom) {
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

            $actor->kabooodle_as_shipping = $command->isKabooodleDefaultShippingProvider();
            $actor->save();

            return $actor;
        });
    }
}
