<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\User;

use Binput;
use Kabooodle\Models\User;
use Illuminate\Http\Request;
use Kabooodle\Transformers\Follows\FollowersTransformer;
use Kabooodle\Http\Controllers\Api\AbstractApiController;

/**
 * Class FollowersApiController
 */
class FollowersApiController extends AbstractApiController
{
    /**
     * @param Request $request
     * @param string  $username
     *
     * @return \Dingo\Api\Http\Response
     */
    public function index(Request $request, string $username)
    {
        /** @var User $user */
        $user = User::where('username', '=', Binput::clean($username))->firstOrFail();

        $data = $user->followers()->paginate(config('pagination.per-page'));

        return $this->response->paginator($data, new FollowersTransformer);
    }
}
