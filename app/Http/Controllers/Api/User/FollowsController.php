<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use Kabooodle\Bus\Commands\Followable\FollowNewEntityCommand;
use Kabooodle\Bus\Commands\Followable\UnfollowEntityCommand;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

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

        try {
            $followable = User::where('id', $id)->first();
            if(!$followable) {
                throw new ModelNotFoundException;
            }

            $this->dispatchNow(new FollowNewEntityCommand($user, $followable));

            return $this->noContent();
        } catch (Exception $e) {
            return $this->setData(['message' => $e->getMessage()])->setStatusCode(500)->respond();
        }
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

        try {
            $followable = User::where('id', $id)->first();
            if (!$followable) {
                throw new ModelNotFoundException;
            }

            $this->dispatchNow(new UnfollowEntityCommand($user, $followable));

            return $this->noContent();
        } catch (Exception $e) {
            return $this->setStatusCode(500)->respond();
        }
    }
}
