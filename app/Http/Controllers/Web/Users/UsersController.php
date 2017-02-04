<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Users;

use Messages;
use Kabooodle\Models\User;
use Illuminate\Http\Request;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Kabooodle\Http\Controllers\Web\Controller;

/**
 * Class UserShopController
 * @package Kabooodle\Http\Controllers\Web\Users
 */
class UsersController extends Controller
{
    /**
     * @param $username
     */
    public function userProfile(Request $request)
    {
        $viewedUser = User::where('username', $request->userName)->first();
        return view('userprofile')->with(compact('viewedUser'));
    }

    public function getUser(Request $request)
    {
        return redirect()->intended($request->get('/home', '/users/'.user()->username));
    }

}
