<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Social;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Social\Facebook\GetUserFacebookGroupsCommand;
use Kabooodle\Bus\Commands\Social\Facebook\RefreshUserFacebookGroupsCommand;
use Kabooodle\Http\Controllers\Api\AbstractApiController;

/**
 * Class RefreshFacebookGroupsController
 */
class RefreshFacebookGroupsController extends AbstractApiController
{
    use DispatchesJobs;

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function refresh(Request $request)
    {
        try {
            $this->validate($request, [
                'accessToken' => 'required',
                'userID' => 'required'
            ]);

            $user = $this->getUser();
            $user->facebook_access_token = $request->get('accessToken');
            $user->save();

            $this->dispatchNow(new RefreshUserFacebookGroupsCommand($user));

            $route = route('inventory.postables');
            $l = \Request::create($route);

            return \Route::dispatch($l);
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)->respond();
        }
    }
}