<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Social\Facebook;

use Illuminate\Cache\Repository as CacheRepository;
use Kabooodle\Services\Social\Facebook\FacebookSdkService;

/**
 * Class UserFacebookCache
 * @package Kabooodle\Bus\Handlers\Commands\Social\Facebook
 */
class UserFacebookCache
{
    const TAG = 'kaboodle_user_fb_groups';

    /**
     * GetUserFacebookGroupsCommandHandler constructor.
     *
     * @param CacheRepository    $cache
     * @param FacebookSdkService $facebook
     */
    public function __construct(CacheRepository $cache, FacebookSdkService $facebook)
    {
        $this->cache = $cache;
        $this->facebook = $facebook;
    }
}
