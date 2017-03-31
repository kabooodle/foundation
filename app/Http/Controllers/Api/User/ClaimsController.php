<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\User;

use DB;
use Binput;
use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Models\User;
use Kabooodle\Transformers\Claims\UserClaimsTransformer;

/**
 * Class ClaimsController
 */
class ClaimsController extends AbstractApiController
{
    /**
     * @param Request $request
     * @param string  $username
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, string $username)
    {
        $user = User::where('username', '=', $username)->firstOrFail();
        $claims = $user->claimsAsBuyer()->paginate(config('pagination.per-page'));;

        return $this->response->paginator($claims, new UserClaimsTransformer);
    }

    /**
     * @param Request $request
     * @param string  $username
     * @param         $claimId
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, string $username, $claimId)
    {
        $user = User::where('username', '=', $username)->firstOrFail();
        $claim = $user->claims->findOrFail($claimId);

        return $this->setData(['claim' => $claim])->respond();
    }
}
