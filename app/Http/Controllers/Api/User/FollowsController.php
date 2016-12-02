<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Api\AbstractApiController;

/**
 * Class LikesApiController
 * @package Kabooodle\Http\Controllers\Api\User
 */
class FollowsController extends AbstractApiController
{
    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Dingo\Api\Http\Response
     */
    public function store(Request $request, $id)
    {
        $user = $this->getUser();
        if (! $user->is_liked) {

        }

        return $this->response()->noContent();
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Dingo\Api\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $user = $this->getUser();
        if ($user->is_liked) {

        }

        return $this->response()->noContent();
    }
}
