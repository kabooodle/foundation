<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Composers;

use JWTAuth;
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
        $view->with_currentUser(user() ? user()->toJson() : '""');
        $view->with_authToken(Auth::user() ? JWTAuth::fromUser(Auth::user()) : null);
    }
}