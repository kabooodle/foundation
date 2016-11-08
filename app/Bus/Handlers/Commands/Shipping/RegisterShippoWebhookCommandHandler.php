<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Shipping;

use Kabooodle\Bus\Commands\Shipping\RegisterShippoWebhookCommand;
use Kabooodle\Services\Shippr\ShipprService;

/**
 * Class RegisterShippoWebhookCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Shipping
 */
class RegisterShippoWebhookCommandHandler
{
    public function __construct(ShipprService $shippr)
    {
        $this->shippr = $shippr;
    }
    public function handle(RegisterShippoWebhookCommand $command)
    {
        // Need to create a post :
        // https://goshippo.com/docs/reference#tracks-create
    }
}