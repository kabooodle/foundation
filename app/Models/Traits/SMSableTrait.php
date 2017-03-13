<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

/**
 * Class SMSableTrait
 */
trait SMSableTrait
{
    static $client;

    /**
     * @return \Illuminate\Foundation\Application|mixed|\Nexmo\Client
     */
    public function getNexmoClient()
    {
        if (! self::$client) {
            self::$client = app(\Nexmo\Client::class);
        }

        return self::$client;
    }
}