<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Auth;

use Auth;
use Kabooodle\Bus\Commands\User\ConvertGuestToUserCommand;
use Kabooodle\Models\Email;
use Messages;
use Validator;
use Kabooodle\Models\User;
use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Bus\Commands\User\AddUserCommand;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Kabooodle\Bus\Events\User\UserLoggedInEvent;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Kabooodle\Http\Middleware\ReferralProgramMiddleware;

/**
 * Class AuthController
 * @package Kabooodle\Http\Controllers\Web\Auth
 */
class AuthController extends Controller
{
    use AuthenticatesUsers {
        login as parentLogin;
    }
    use ThrottlesLogins;

    /**
     * @var string
     */
    protected $loginView = 'auth.login';

    /**
     * @var string
     */
    protected $username = 'username';

    /**
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * @var string
     */
    protected $redirectAfterLogout = '/';

    /**
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    /**
     * @param  array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, User::getRules());
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function getRegister()
    {
        return $this->view('auth.register');
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postRegister(Request $request)
    {
        try {
            $email = Email::whereAddress(trim($request->get('email')))->first();

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
                    $request->session()->get(ReferralProgramMiddleware::SESSION_KEY)
                ));
            } else {
                $this->validate($request, User::getRules(), ['email.unique' => 'Email address is unavailable.']);

                $user = $this->dispatch(new AddUserCommand(
                    $request->get('first_name'),
                    $request->get('last_name'),
                    $request->get('username'),
                    $request->get('email'),
                    $request->get('password'),
                    $request->session()->get(ReferralProgramMiddleware::SESSION_KEY)
                ));
            }

            Auth::attempt([
                'username' => $user->username,
                'password' => $request->get('password')
            ]);

            Messages::success("Welcome to ".env('APP_NAME').", {$user->first_name}!");

            return $this->redirect($request->get('_redirect', '/'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            Messages::error($e->validator->getMessageBag()->first());

            return $this->redirect(route('auth.register'))
                ->withInput($request->all())
                ->withErrors($e->validator->getMessageBag());
        }
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try {
            $this->validateLogin($request);

            $this->parentLogin($request);
            return redirect()->intended($request->get('_redirect', $this->redirectTo));
        } catch (\Illuminate\Validation\ValidationException $e) {
            Messages::error($e->validator->getMessageBag()->first());

            return $this->redirect(route('auth.login'))
                ->withErrors($e->validator->getMessageBag());
        } catch (Exception $e) {
            return $this->redirect(route('auth.login'));
        }
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, User $user)
    {
        event(new UserLoggedInEvent($user));

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Get the failed login response instance.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        Messages::error($this->getFailedLoginMessage());

        return redirect()->back()
            ->withInput($request->only($this->loginUsername(), 'remember'))
            ->withErrors([
                $this->loginUsername() => $this->getFailedLoginMessage(),
            ]);
    }
}
