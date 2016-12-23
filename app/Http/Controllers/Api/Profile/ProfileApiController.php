<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Profile;

use Exception;
use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Bus\Commands\Subscriptions\SubscribeUserToGenericTrialCommand;

/**
 * Class ProfileApiController
 */
class ProfileApiController extends AbstractApiController
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function subscribeToTrial(Request $request)
    {
        try {
            $user = $this->getUser();
            $this->dispatchNow(new SubscribeUserToGenericTrialCommand($user));

            return $this->setData(['msg' => 'Ok', 'redirect' => route('')])->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)->respond();
        }
    }
}