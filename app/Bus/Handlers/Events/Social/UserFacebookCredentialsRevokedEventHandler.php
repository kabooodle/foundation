<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Social;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Events\Social\UserFacebookCredentialsRevokedEvent;
use Kabooodle\Bus\Commands\Social\Facebook\RefreshUserFacebookGroupsCommand;

/**
 * Class UserFacebookCredentialsRevokedEvent
 * @package Kabooodle\Bus\Handlers\Events\Social
 */
class UserFacebookCredentialsRevokedEventHandler
{
    use DispatchesJobs;

    /**
     * @param UserFacebookCredentialsRevokedEvent $event
     */
    public function handle(UserFacebookCredentialsRevokedEvent $event)
    {
        $this->dispatch(new RefreshUserFacebookGroupsCommand($event->getActor()));
    }
}
