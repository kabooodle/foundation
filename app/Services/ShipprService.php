<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

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