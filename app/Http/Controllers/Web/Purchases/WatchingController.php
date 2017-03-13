<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Purchases;

use Illuminate\Http\Request;
use Kabooodle\Models\Watches;
use Kabooodle\Http\Controllers\Web\Controller;

/**
 * Class WatchingController
 * @package Kabooodle\Http\Controllers\Web\Follow
 */
class WatchingController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $watching = webUser()->watching;

        return view('purchases.watching')->with(compact('watching'));
    }
}
