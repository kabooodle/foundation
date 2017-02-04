<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Referrals;

use Kabooodle\Models\User;
use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Web\Controller;

/**
 * Class ReferralsController
 * @package Kabooodle\Http\Controllers\Web\Referrals
 */
class ReferralsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return $this->view('referrals.index');
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function invite(Request $request)
    {
        $referrer = User::where('username', $request->userName)->first();
        return view('auth.register')->with(compact('referrer'));
    }
}
