<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Social\Facebook;

use Kabooodle\Bus\Events\CacheMissEvent;
use Facebook\Exceptions\FacebookSDKException;
use Kabooodle\Bus\Commands\Social\Facebook\GetUserFacebookGroupsCommand;
use Kabooodle\Models\User;

/**
 * Class GetUserFacebookGroupsCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Social\Facebook
 */
class GetUserFacebookGroupsCommandHandler extends UserFacebookCache
{
    /**
     * @param GetUserFacebookGroupsCommand $command
     *
     * @return array|mixed
     * @throws FacebookSDKException
     */
    public function handle(GetUserFacebookGroupsCommand $command)
    {
        $tag = self::TAG;
        /** @var User $actor */
        $actor = $command->getActor();
        if($actor->getFacebookUserId() || $actor->getFacebookUserToken()) {
            return [];
        }
//        if ($this->cache->tags($tag)->has($actor->getFacebookUserId())) {
//            return $this->cache->tags($tag)->get($actor->getFacebookUserId());
//        }

//        event(new CacheMissEvent($tag, $actor->getFacebookUserId()));

        try {
            $groups = $this->facebook->getUsersGroups($actor->getFacebookUserId());
            if ($groups) {
                foreach ($groups as $key => $group) {

                    // If the user cannot administrate, delete the group.
                    if ($group['administrator'] === false) {
                        unset($groups[$key]);
                        continue;
                    }

                    // If the group has albums, lets make sure the album can be uploaded to.
                    if (isset($group['albums'])) {
                        foreach ($group['albums'] as $albumKey => $album) {
                            if ($album['can_upload'] === false) {
                                unset($group['albums'][$albumKey]);
                            }
                        }
                    } else {

                        // Create an empty albums key with an empty array.
                        $group['albums'] = [];
                    }
                }
            }
//            foreach ($groups as &$group) {
//                $group['albums'] = $this->facebook->getGroupAlbums($group['id'])->asArray();
//            }
//            $this->cache->tags($tag)->put($actor->getFacebookUserId(), $groups, config('session.lifetime'));

            return $groups;
        } catch (FacebookSDKException $e) {
            throw $e;
        }
    }
}
