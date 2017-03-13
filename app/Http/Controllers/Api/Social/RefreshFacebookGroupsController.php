<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Social;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\User\ExtendFacebookAccessTokenCommand;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Services\Social\Facebook\FacebookSdkService;
use Kabooodle\Bus\Commands\Social\Facebook\RefreshUserFacebookGroupsCommand;

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

            $this->dispatch(new ExtendFacebookAccessTokenCommand($this->getUser(), $request->get('accessToken')));

            $this->dispatchNow(new RefreshUserFacebookGroupsCommand($this->getUser()));

            $route = route('inventory.postables');
            $l = \Request::create($route);

            return \Route::dispatch($l);
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)->respond();
        }
    }
}
