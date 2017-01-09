<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Foundation\Providers;

use Vinkla\Pusher\PusherServiceProvider as BasePusherServiceProvider;

/**
 * Class PusherServiceProvider
 */
class PusherServiceProvider extends BasePusherServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;
}