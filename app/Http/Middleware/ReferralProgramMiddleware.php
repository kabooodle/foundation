<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Middleware;

use Auth;
use Binput;
use Closure;
use Kabooodle\Services\Referrals\ReferralsService;

/**
 * Class ReferralProgramMiddleware
 * @package Kabooodle\Http\Middleware
 */
class ReferralProgramMiddleware
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
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  string|null              $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $response = $next($request);

        if (Auth::guard($guard)->guest() && !$this->sessionValueExists($request)) {
            $this->setSessionValue($request);
        }

        return $response;
    }

    /**
     * @param  \Illuminate\Http\Request $request
     *
     * @return bool
     */
    public function sessionValueExists($request)
    {
        return (bool) $request->session()->has($this->referralService::REFERRAL_BY_USERNAME) && $request->session()->get($this->referralService::REFERRAL_BY_USERNAME) == trim($request->username);
    }

    /**
     * @param  \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function setSessionValue($request)
    {
        if ($user = $this->referralService->lookupRefereeByUsername(Binput::clean($request->username))) {
            $request->session()->put($this->referralService::REFERRAL_BY_USERNAME, $user->username);
        }
    }
}
