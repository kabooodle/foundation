<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\User;

use DB;
use Kabooodle\Models\Address;
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
        $formShipTo = $command->getToAddress();

        return DB::transaction(function () use ($command, $actor, $formShipFrom, $formShipTo) {
            $primaryShipFromAddress = $actor->primaryShipFromAddress ? : new Address;
            $primaryShipFromAddress->user_id = $actor->id;
            $primaryShipFromAddress->type = Address::TYPE_FROM;
            $primaryShipFromAddress->primary = 1;
            $primaryShipFromAddress->street1 = $formShipFrom->getStreet1();
            $primaryShipFromAddress->street2 = $formShipFrom->getStreet2();
            $primaryShipFromAddress->city = $formShipFrom->getCity();
            $primaryShipFromAddress->state = $formShipFrom->getState();
            $primaryShipFromAddress->zip = $formShipFrom->getZip();
            $primaryShipFromAddress->save();

            $primaryShipToAddress = $actor->primaryShipToAddress ? : new Address;
            $primaryShipToAddress->user_id = $actor->id;
            $primaryShipToAddress->type = Address::TYPE_TO;
            $primaryShipToAddress->primary = 1;
            $primaryShipToAddress->street1 = $formShipTo->getStreet1();
            $primaryShipToAddress->street2 = $formShipTo->getStreet2();
            $primaryShipToAddress->city = $formShipTo->getCity();
            $primaryShipToAddress->state = $formShipTo->getState();
            $primaryShipToAddress->zip = $formShipTo->getZip();
            $primaryShipToAddress->save();

            $actor->kabooodle_as_shipping = $command->isKabooodleDefaultShippingProvider();
            $actor->save();

            return $actor;
        });
    }
}
