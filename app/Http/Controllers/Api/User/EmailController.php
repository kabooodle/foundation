<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\User;

use Exception;
use Illuminate\Http\Request;
use Kabooodle\Bus\Commands\Email\AddEmailCommand;
use Kabooodle\Bus\Commands\Email\DestroyEmailCommand;
use Kabooodle\Bus\Commands\Email\MakeEmailPrimaryCommand;
use Kabooodle\Bus\Commands\Email\ResendEmailVerificationCommand;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Models\Email;

/**
 * Class EmailController
 * @package Kabooodle\Http\Controllers\Api\User
 */
class EmailController extends AbstractApiController
{
    /**
     * @param Request $request
     *
     * @return \Dingo\Api\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $emails = Email::whereUserId($this->getUser()->id)->get();

            return $this->setData(['emails' => $emails])->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)->respond($e);
        }
    }

    /**
     * @param Request $request
     *
     * @return \Dingo\Api\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, Email::getRules());

            $email = $this->dispatchNow(new AddEmailCommand(
                $this->getUser(),
                $request->get('address'),
                $request->get('primary', false)));

            return $this->setData(['email' => $email])->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)->respond($e);
        }
    }

    /**
     * @param Request $request
     *
     * @return \Dingo\Api\Http\Response
     */
    public function update(Request $request, $userId, $emailId)
    {
        try {
            $email = Email::whereId($emailId)->whereUserId($this->getUser()->id)->whereVerified(1)->firstOrFail();

            $this->dispatchNow(new MakeEmailPrimaryCommand($email));

            return $this->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)->respond();
        }
    }

    /**
     * @param Request $request
     *
     * @return \Dingo\Api\Http\Response
     */
    public function updatePrimary(Request $request)
    {
        try {
            $email = Email::whereId($request->get('email_id'))->whereUserId($this->getUser()->id)->whereVerified(1)->firstOrFail();

            $this->dispatchNow(new MakeEmailPrimaryCommand($email));

            return $this->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)->respond();
        }
    }

    /**
     * @param Request $request
     *
     * @return \Dingo\Api\Http\Response
     */
    public function resendVerification(Request $request)
    {
        try {
            $email = Email::whereId($request->get('email_id'))->whereUserId($this->getUser()->id)->firstOrFail();

            $this->dispatchNow(new ResendEmailVerificationCommand($email));

            return $this->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)->respond();
        }
    }

    /**
     * @param Request $request
     * @param         $emailId
     *
     * @return \Dingo\Api\Http\Response
     */
    public function destroy(Request $request, $userId, $emailId)
    {
        try {
            $email = Email::whereId($emailId)->whereUserId($this->getUser()->id)->wherePrimary(false)->firstOrFail();

            $this->dispatchNow(new DestroyEmailCommand($email));

            return $this->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)->respond();
        }
    }
}
