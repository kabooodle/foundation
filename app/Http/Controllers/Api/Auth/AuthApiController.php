<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Auth;

use Tymon\JWTAuth\JWTAuth;
use Kabooodle\Http\Requests\Request;
use Dingo\Api\Exception\InternalHttpException;
use Kabooodle\Http\Controllers\Api\AbstractApiController;

/**
 * Class AuthApiController
 * @package Kabooodle\Http\Controllers\Api\Auth
 */
class AuthApiController extends AbstractApiController
{
    /**
     * AuthApiController constructor.
     *
     * @param JWTAuth $auth
     */
    public function __construct(JWTAuth $auth)
    {
        $this->_auth = $auth;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(\Illuminate\Http\Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');
        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = $this->_auth->attempt($credentials)) {
                return $this->response()->errorUnauthorized('invalid_credentials');
            }
        } catch (InternalHttpException $e) {
            // something went wrong whilst attempting to encode the token
            return $this->response()->errorInternal('could_not_create_token');
        }

        // all good so return the token
        return $this->setData(['token' => $token])->respond();
    }
}
