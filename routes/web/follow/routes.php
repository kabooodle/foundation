<?php

Route::get('{username}/followers', [
    'as' => 'follow.followers',
    'uses' => \Kabooodle\Http\Controllers\Web\Follow\FollowController::class.'@followers'
]);

Route::get('{username}/following', [
    'as' => 'follow.following',
    'uses' => \Kabooodle\Http\Controllers\Web\Follow\FollowController::class.'@following'
]);