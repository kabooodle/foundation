<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Api\AbstractApiController;

/**
 * Class LikesApiController
 * @package Kabooodle\Http\Controllers\Api\User
 */
class LikesApiController extends AbstractApiController
{
    /**
     * @param Request $request
     * @param         $username
     *
     * @return \Dingo\Api\Http\Response
     */
    public function post(Request $request, $username)
    {
        $user = $this->getUser();
        if (! $user->is_liked) {
        }

        return $this->response()->noContent();
    }

    /**
     * @param Request $request
     * @param         $username
     *
     * @return \Dingo\Api\Http\Response
     */
    public function destroy(Request $request, $username)
    {
        $user = $this->getUser();
        if ($user->is_liked) {
        }

        return $this->response()->noContent();
    }
}
