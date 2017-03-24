<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Auth;

use Kabooodle\Bus\Commands\User\AddUserCommand;
use Kabooodle\Bus\Commands\User\ConvertGuestToUserCommand;
use Kabooodle\Bus\Events\User\UserLoggedInEvent;
use Kabooodle\Models\Email;
use Kabooodle\Models\User;
use Kabooodle\Services\Referrals\ReferralsService;
use Tymon\JWTAuth\JWTAuth;
use Kabooodle\Http\Requests\Request;
use Dingo\Api\Exception\InternalHttpException;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Binput;

/**
 * Class AuthApiController
 * @package Kabooodle\Http\Controllers\Api\Auth
 */
class AuthApiController extends AbstractApiController
{
    /**
     * @var ReferralsService
     */
    public $referralService;

    /**
     * AuthApiController constructor.
     *
     * @param JWTAuth $auth
     * @param ReferralsService $referralsService
     */
    public function __construct(JWTAuth $auth, ReferralsService $referralsService)
    {
        $this->_auth = $auth;
        $this->referralService = $referralsService;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(\Illuminate\Http\Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('username', 'password');
        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = $this->_auth->attempt($credentials)) {
                return $this->response()->errorUnauthorized('invalid_credentials');
            }
        } catch (InternalHttpException $e) {
            // something went wrong whilst attempting to encode the token
            return $this->response()->errorInternal('could_not_create_token');
        }

//        event(new UserLoggedInEvent($this->getUser()));

        // all good so return the token
        return $this->setData(['token' => $token])->respond();
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function register(\Illuminate\Http\Request $request)
    {
        try {
            $email = Email::whereAddress(Binput::clean($request->get('email')))->first();

            $referral = $this->referralService->getReferral() ? : Binput::get('referred_by', null);

            if ($email && $email->user->isGuest()) {
                $guest = $email->user;
                $this->validate($request, User::getConvertGuestRules($guest));

                $user = $this->dispatch(new ConvertGuestToUserCommand(
                    $guest,
                    $email,
                    $request->get('first_name'),
                    $request->get('last_name'),
                    $request->get('username'),
                    $request->get('password'),
                    $referral
                ));
            } else {
                $this->validate($request, User::getRules(), ['email.unique' => 'Email address is unavailable.']);
                $user = $this->dispatch(new AddUserCommand(
                    $request->get('first_name'),
                    $request->get('last_name'),
                    $request->get('username'),
                    $request->get('email'),
                    $request->get('password'),
                    $request->get('account_type'),
                    $request->get('timezone'),
                    $referral
                ));
            }

            $credentials = $request->only('username', 'password');

            try {
                // attempt to verify the credentials and create a token for the user
                if (! $token = $this->_auth->attempt($credentials)) {
                    return $this->response()->errorUnauthorized('invalid_credentials');
                }
            } catch (InternalHttpException $e) {
                // something went wrong whilst attempting to encode the token
                return $this->response()->errorInternal('could_not_create_token');
            }

//            event(new UserLoggedInEvent($this->getUser()));

            return $this->setData([
                'token' => $token,
                'msg' => "Welcome to ".env('APP_NAME').", {$user->first_name}!",
            ])->respond();
        } catch (ValidationException $e) {
            return $this->setStatusCode(401)->setData([
                'msg' => $e->validator->getMessageBag()->first(),
            ])->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)->setData([
                'msg' => 'An error occurred, please try again.',
            ])->respond();
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function guestConvert(\Illuminate\Http\Request $request)
    {
        try {
            $email = Email::whereAddress(Binput::clean($request->get('email')))->first();

            $referral = $this->referralService->getReferral() ? : Binput::get('referred_by', null);

            if ($email && $email->user->isGuest()) {
                $guest = $email->user;
                $this->validate($request, User::getConvertGuestRules($guest));

                $user = $this->dispatch(new ConvertGuestToUserCommand(
                    $guest,
                    $email,
                    $guest->first_name,
                    $guest->last_name,
                    $request->get('username'),
                    $request->get('password'),
                    $referral
                ));
            }

            $credentials = $request->only('username', 'password');

            try {
                // attempt to verify the credentials and create a token for the user
                if (! $token = $this->_auth->attempt($credentials)) {
                    return $this->response()->errorUnauthorized('invalid_credentials');
                }
            } catch (InternalHttpException $e) {
                // something went wrong whilst attempting to encode the token
                return $this->response()->errorInternal('could_not_create_token');
            }

//            event(new UserLoggedInEvent($this->getUser()));

            return $this->setData([
                'token' => $token,
                'msg' => "Welcome to ".env('APP_NAME').", {$user->username}!",
            ])->respond();
        } catch (ValidationException $e) {
            return $this->setStatusCode(401)->setData([
                'msg' => $e->validator->getMessageBag()->first(),
            ])->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)->setData([
                'msg' => 'An error occurred, please try again.',
            ])->respond();
        }
    }
}
