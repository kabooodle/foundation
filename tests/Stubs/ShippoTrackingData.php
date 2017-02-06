<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Tests\Stubs;

/**
 * Class ShippoTrackingData
 */
class ShippoTrackingData
{
    /**
     * @return mixed
     */
    public function getTrackingUpdate()
    {
        $json = __DIR__ . DIRECTORY_SEPARATOR . 'json/shippo.trackingdata.delivered.json';

        return json_decode(file_get_contents($json), true);
    }
}