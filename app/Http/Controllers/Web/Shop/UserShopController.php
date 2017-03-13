<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Shop;

use Messages;
use Kabooodle\Models\User;
use Illuminate\Http\Request;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Kabooodle\Http\Controllers\Web\Controller;

/**
 * Class UserShopController
 * @package Kabooodle\Http\Controllers\Web\Shop
 */
class UserShopController extends Controller
{
    use ObfuscatesIdTrait;

    /**
     * @param $username
     */
    public function index($username)
    {
    }

    /**
     * @param $username
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Http\RedirectResponse|null|static
     */
    public function show($username)
    {
        $user = User::where('username', $username)->first();
        if ($user) {
            return $this->view('shop.shop')->with(compact('user'));
        }

        return redirect()->back();
    }
}
