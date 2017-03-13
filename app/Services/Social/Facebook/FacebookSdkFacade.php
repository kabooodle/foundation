<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Services\Social\Facebook;

use Illuminate\Support\Facades\Facade;

/**
 * Class FacebookSdkFacade
 * @package Kabooodle\Services\Social\Facebook
 */
class FacebookSdkFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Kabooodle\Services\Social\Facebook\FacebookSdkService';
    }
}
