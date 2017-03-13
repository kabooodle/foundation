<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Composers;

use JWTAuth;
use Analytics;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

/**
 * Class CurrentUserComposer
 * @package Kabooodle\Composers
 */
class CurrentUserComposer
{
    /**
     * @param View $view
     */
    public function compose(View $view)
    {
        $user = Auth::user();
        if ($user) {
            $user->setRelations([])->load(['usersFollowing', 'followers']);
        }
        $view->with_currentUser($user ? $user->toJson() : json_encode(null));
        $view->with_authToken($user ? JWTAuth::fromUser($user) : null);
    }
}
