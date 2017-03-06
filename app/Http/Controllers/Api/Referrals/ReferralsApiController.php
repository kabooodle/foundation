<?php

namespace Kabooodle\Http\Controllers\Api\Referrals;

use Kabooodle\Models\User;
use Illuminate\Http\Request;
use Kabooodle\Transformers\Users\UserReferrals;
use Kabooodle\Http\Controllers\Api\AbstractApiController;

/**
 * Class ReferralsApiController
 */
class ReferralsApiController extends AbstractApiController
{
    /**
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->response->collection($user->referrals, new UserReferrals);
    }
}