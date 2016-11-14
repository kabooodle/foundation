<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Profile;

use Binput;
use Messages;
use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Web\Controller;


/**
 * Class ProfileCreditCardController
 * @package Kabooodle\Http\Controllers\Web\Profile
 */
class ProfilePurchasesController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $claims = user()->claimsAsBuyer();
        return view('profile.purchases')->with(compact('claims'));
    }

}