<?php

namespace Kabooodle\Http\Controllers\Web\Referrals;

use Kabooodle\Http\Controllers\Web\Controller;

class ReferralsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return $this->view('referrals.index');
    }
}