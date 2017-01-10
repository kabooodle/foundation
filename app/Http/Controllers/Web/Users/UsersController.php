<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
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
        $user = User::where('username', $request->userName)->first();
        return view('welcome')->with(compact('user'));
    }

}
