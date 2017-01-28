<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Follow;

use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Traits\PaginatesTrait;
use Kabooodle\Models\User;
use Kabooodle\Models\Watches;
use Kabooodle\Http\Controllers\Web\Controller;

/**
 * Class FollowController
 * @package Kabooodle\Http\Controllers\Web\Follow
 */
class FollowController extends Controller
{
    use PaginatesTrait;

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function followers(Request $request)
    {
        $viewedUser = User::where('username', $request->username)->first();

        $followers = $viewedUser->followers;

        //$followers = $this->paginateData($request, $user->followers);

        return view('follow.followers')->with(compact('viewedUser', 'followers'));
    }

    public function following(Request $request)
    {
        $viewedUser = User::where('username', $request->username)->first();

        $usersFollowing = $this->paginateData($request, $viewedUser->usersFollowing);

        return view('follow.following')->with(compact('viewedUser', 'usersFollowing'));
    }
}