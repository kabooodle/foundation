<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Profile;

use Exception;
use Illuminate\Http\Request;
use Kabooodle\Bus\Commands\User\UpdateUserShippingProfileCommand;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Bus\Commands\Subscriptions\SubscribeUserToGenericTrialCommand;
use Kabooodle\Foundation\Exceptions\Subscription\UserAlreadyHadFreeTrialException;

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

            return $this->setData([
                'msg' => 'Congratulations! Your account has been upgraded to Merchant Plus Plan for a free 30 days.',
                'redirect' => '/'
            ])->respond();
        } catch (UserAlreadyHadFreeTrialException $e) {
            return $this->setStatusCode(500)->setData([
                'msg' => 'Were sorry, you have already used the 30 day free trial.'
            ])->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)->setData([
                'msg' => 'An error occurred. Please try again.'
            ])->respond();
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function updateShippingProfile(Request $request)
    {
        try {
            $user = $this->getUser();
            $this->dispatchNow(new UpdateUserShippingProfileCommand($user, $request->get('use_kabooodle_as_shipper')));

            return $this->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)->respond();
        }
    }
}