<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Console\Commands\Subscription;

use Illuminate\Console\Command;
use Kabooodle\Models\User;

/**
 * Class AbstractConsoleNotification
 * @package Kabooodle\Console\Commands\Subscription
 */
abstract class AbstractConsoleNotification extends Command
{
    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param User $recipient
     */
    public function logNotificationHandled(User $recipient)
    {

    }
}
