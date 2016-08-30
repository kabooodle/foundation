<?php

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
        $view->with_currentUser(Auth::user());
        $view->with_authToken(Auth::user() ? JWTAuth::fromUser(Auth::user()) : null);
    }
}