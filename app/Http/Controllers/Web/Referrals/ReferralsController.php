<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Referrals;

use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Services\Referrals\ReferralsService;

/**
 * Class ReferralsController
 * @package Kabooodle\Http\Controllers\Web\Referrals
 */
class ReferralsController extends Controller
{
    /**
     * @var ReferralsService
     */
    public $referralService;

    /**
     * @param ReferralsService $referralsService
     */
    public function __construct(ReferralsService $referralsService)
    {
        $this->referralService = $referralsService;
    }

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
        $referrer = $this->referralService->getReferral();
        if (! $referrer) {
            return redirect()->route('auth.register');
        }

        return view('auth.register')->with(compact('referrer'));
    }
}
