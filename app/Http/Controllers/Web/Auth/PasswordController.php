<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Auth;

use Auth;
use Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Kabooodle\Http\Controllers\Web\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Kabooodle\Bus\Events\User\UserPasswordWasResetEvent;

/**
 * Class PasswordController
 * @package Kabooodle\Http\Controllers\Web\Auth
 */
class PasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * @var string
     */
    public $redirectPath = '/';

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function getResetValidationRules()
    {
        return [
            '_token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ];
    }

    /**
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function reset(Request $request)
    {
        try {
            $this->validate(
                $request,
                $this->getResetValidationRules()
            );

            $credentials = $request->only(
                'email', 'password', 'password_confirmation', 'token'
            );

            $broker = $this->getBroker();

            $response = Password::broker($broker)->reset($credentials, function ($user, $password) {
                $this->resetPassword($user, $password);
            });

            switch ($response) {
                case Password::PASSWORD_RESET:

                    event(new UserPasswordWasResetEvent(Auth::getUser()));

                    Messages::success('Your password has been changed!');

                    return $this->getResetSuccessResponse($response);

                default:
                    Messages::error('Error resetting password.');

                    return $this->getResetFailureResponse($request, $response);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Messages::error($e->validator->getMessageBag()->first());

            return $this->redirect()->back()
                ->withErrors($e->validator->getMessageBag());
        }
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendResetLinkEmail(Request $request)
    {
        try {
            $this->validate($request, ['email' => 'required|email']);
            $broker = $this->getBroker();

            $response = Password::broker($broker)->sendResetLink(
                $request->only('email'), $this->resetEmailBuilder()
            );

            switch ($response) {
                case Password::RESET_LINK_SENT:

                    Messages::success('Please check your email for instructions on resetting your password.');

                    return $this->getSendResetLinkEmailSuccessResponse($response);

                case Password::INVALID_USER:
                default:

                    Messages::error('Please check the email address.');

                    return $this->getSendResetLinkEmailFailureResponse($response);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Messages::error($e->validator->getMessageBag()->first());

            return $this->redirect(route('auth.password.reset.index'))
            ->withErrors($e->validator->getMessageBag());
        }
    }
}
