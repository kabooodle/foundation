<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
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
     * @param Request $request
     *
     * @return $this
     */
    public function userProfile(Request $request)
    {
        $data = [
            'viewedUser' => User::where('username', $request->username)->firstOrFail(),
        ];

        return view('users.'. ($data['viewedUser']->hasAtLeastMerchantSubscription() ? 'listings' : 'claims'). '.index', $data);
    }

    public function getUser(Request $request)
    {
        return redirect()->intended($request->get('/home', '/users/'.webUser()->username));
    }
}
