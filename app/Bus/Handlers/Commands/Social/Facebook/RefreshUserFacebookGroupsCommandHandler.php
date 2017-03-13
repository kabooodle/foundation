<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Social\Facebook;

use Kabooodle\Bus\Commands\Social\Facebook\RefreshUserFacebookGroupsCommand;

/**
 * Class RefreshUserFacebookGroupsCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Social\Facebook
 */
class RefreshUserFacebookGroupsCommandHandler extends UserFacebookCache
{
    /**
     * @param RefreshUserFacebookGroupsCommand $command
     */
    public function handle(RefreshUserFacebookGroupsCommand $command)
    {
        $tag = self::TAG;
        $actor = $command->getActor();
        if ($this->cache->tags($tag)->has($actor->getFacebookUserId())) {
            $this->cache->tags($tag)->forget($actor->getFacebookUserId());
        }
    }
}
