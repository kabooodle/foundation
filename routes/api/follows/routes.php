<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */


$api->get('users/{username}/followers', [
    'as' => 'followers.index',
    'uses' => \Kabooodle\Http\Controllers\Api\User\FollowersApiController::class.'@index'
]);

$api->get('users/{username}/following', [
    'as' => 'following.index',
    'uses' => \Kabooodle\Http\Controllers\Api\User\FollowingApiController::class.'@index'
]);