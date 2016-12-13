<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Purchases;

use Kabooodle\Models\User;
use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Models\Watches;

/**
 * Class ReferralsController
 * @package Kabooodle\Http\Controllers\Web\Referrals
 */
class WatchingController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $watching = user()->watching;

        return view('purchases.watching')->with(compact('watching'));
    }

    public function destroy(Request $request, $userName, $itemID)
    {
        $watch = Watches::where('id', $itemID)->first();
        $watch->delete();
        return back();
    }
}