<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Users;

use Kabooodle\Models\User;
use Illuminate\Http\Request;
use Kabooodle\Models\Listings;
use Kabooodle\Http\Controllers\Web\Controller;

/**
 * Class UserListingsController
 */
class UserListingsController extends Controller
{
    /**
     * @param Request $request
     * @param         $username
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request, $username)
    {
        $user = User::where('username', '=', $username)->first();

        if (! $user) {
            return abort(404);
        }

        return $this->view('users.listings.index')->with(compact('user'));
    }

    /**
     * @param Request $request
     * @param         $username
     * @param         $listingId
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Request $request, $username, $listingId)
    {
        $user = User::where('username', '=', $username)->first();

        if (! $user) {
            return abort(404);
        }

        return $this->view('users.listings.show')->with(compact('user', 'listingId'));
    }
}
