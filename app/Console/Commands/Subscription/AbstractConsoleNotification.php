<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Console\Commands\Subscription;

use Kabooodle\Models\User;
use Illuminate\Console\Command;

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
