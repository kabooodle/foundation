<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Follow;

use Illuminate\Http\Request;
use Kabooodle\Models\User;
use Kabooodle\Models\Watches;
use Kabooodle\Http\Controllers\Web\Controller;

/**
 * Class FollowController
 * @package Kabooodle\Http\Controllers\Web\Follow
 */
class FollowController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function followers(Request $request)
    {
        $user = User::where('username', $request->username)->first();
        $followers = $user->followers;

        return view('follow.followers')->with(compact('followers'));
    }

    public function following()
    {
        $follow = user()->following;

        return view('follow.follow')->with(compact('follow'));
    }
}