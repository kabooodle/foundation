<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Social\Facebook;

use Kabooodle\Bus\Events\CacheMissEvent;
use Facebook\Exceptions\FacebookSDKException;
use Kabooodle\Bus\Commands\Social\Facebook\GetUserFacebookGroupsCommand;

/**
 * Class GetUserFacebookGroupsCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Social\Facebook
 */
class GetUserFacebookGroupsCommandHandler extends UserFacebookCache
{
    /**
     * @param GetUserFacebookGroupsCommand $command
     *
     * @return array|mixed|void
     */
    public function handle(GetUserFacebookGroupsCommand $command)
    {
        $tag = self::TAG;
        $actor = $command->getActor();
        if ($this->cache->tags($tag)->has($actor->getFacebookUserId())) {
            return $this->cache->tags($tag)->get($actor->getFacebookUserId());
        }

        event(new CacheMissEvent($tag, $actor->getFacebookUserId()));

        try {
            $groups = $this->facebook->getUsersGroups($actor->getFacebookUserId())->asArray();
            foreach ($groups as &$group) {
                $group['albums'] = $this->facebook->getGroupAlbums($group['id'])->asArray();
            }
            $this->cache->tags($tag)->put($actor->getFacebookUserId(), $groups, config('session.lifetime'));

            return $groups;
        } catch (FacebookSDKException $e) {
            throw $e;
        }
    }
}