<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Referrals;

use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Models\User;

class ReferralsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return $this->view('referrals.index');
    }

    public function show(Request $request)
    {
        if(User::where('username', $request->userName)->first()) {
            $user = User::where('username', $request->userName)->first();
            $request->session()->put('referredBy', $user->id);
        }
        else {
            return $this->redirect(route('profile.addresses.edit'));
        }

        return $this->redirect(route(''));
        
        //return $request->session()->get('referred_by');
    }
}