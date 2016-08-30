<?php

namespace Kabooodle\Services;

use Shippo;

/**
 * Class ShipprService
 * @package Kabooodle\Services
 */
class ShipprService
{
    /**
     * ShipprService constructor.
     */
    public function __construct()
    {
        Shippo::setApiKey(env('SHIPPO_PRIVATE'));
    }
}