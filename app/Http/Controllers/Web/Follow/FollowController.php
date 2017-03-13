<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
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
     * @param Request $request
     *
     * @return $this
     */
    public function followers(Request $request)
    {
        $viewedUser = User::where('username', $request->username)->first();

        return view('users.follow.followers')->with(compact('viewedUser'));
    }

    /**
     * @param Request $request
     *
     * @return $this
     */
    public function following(Request $request)
    {
        $viewedUser = User::where('username', $request->username)->first();

        return view('users.follow.following')->with(compact('viewedUser'));
    }
}
