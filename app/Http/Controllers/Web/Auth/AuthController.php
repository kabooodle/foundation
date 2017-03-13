<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Auth;

use Auth;
use Binput;
use Kabooodle\Services\Referrals\ReferralsService;
use Kabooodle\Services\User\UserService;
use Messages;
use Validator;
use Exception;
use Kabooodle\Models\User;
use Kabooodle\Models\Email;
use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Bus\Commands\User\AddUserCommand;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Kabooodle\Bus\Events\User\UserLoggedInEvent;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Kabooodle\Http\Middleware\ReferralProgramMiddleware;
use Kabooodle\Bus\Commands\User\ConvertGuestToUserCommand;

/**
 * Class AuthController
 * @package Kabooodle\Http\Controllers\Web\Auth
 */
class AuthController extends Controller
{
    use AuthenticatesUsers {
        login as parentLogin;
    }
//    use ThrottlesLogins;

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
    protected $redirectAfterLogout = '/home';

    /**
     * @var UserService
     */
    public $userService;

    /**
     * @var ReferralsService
     */
    public $referralService;

    /**
     * @param UserService      $userService
     * @param ReferralsService $referralsService
     */
    public function __construct(UserService $userService, ReferralsService $referralsService)
    {
        $this->userService = $userService;
        $this->referralService = $referralsService;
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
     * @param Request $request
     *
     * @return $this|\Illuminate\Contracts\View\View
     */
    public function getRegister(Request $request)
    {
        if (app()->environment() == 'production' || $request->has('closedbeta')) {
            if ($request->get('p') <> 'lacroix') {
                return $this->view('auth.comingsoon');
            }
        }

        $referrer = $this->referralService->getReferral();

        return $this->view('auth.register')->with(compact('referrer'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postRegister(Request $request)
    {
        try {
            $email = Email::whereAddress(Binput::clean($request->get('email')))->first();
            $redirect = Binput::get('_redirect', false);
            if (! $redirect || $redirect == '') {
                $redirect = '/';
            }

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
                    $referral
                ));
            }

            Auth::attempt([
                'username' => $user->username,
                'password' => $request->get('password')
            ]);

            event(new UserLoggedInEvent($user));

            Messages::success("Welcome to ".env('APP_NAME').", {$user->first_name}!");

            if ($redirect == '/') {
                $redirect = route('profile.index');
            }

            return $this->redirect($redirect);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Messages::error($e->validator->getMessageBag()->first());

            return $this->redirect(route('auth.register'))
                ->withInput($request->all())
                ->withErrors($e->validator->getMessageBag());
        } catch (Exception $e) {
            Messages::error('An error occurred, please try again.');

            return $this->redirect(route('auth.register'))
                ->withInput($request->all());
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
            $this->parentLogin($request);

            return redirect()->intended($request->get('/home', '/users/'.$request->username));
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
     * @throws \Exception
     */
//    public function authenticated(Request $request, User $user)
//    {
////        if (! $user->accountActivated()) {
////            Auth::guard($this->getGuard())->logout();
////
////            Messages::error('Your primary email address has not yet been verified. Please check your email.');
////
////            throw new Exception;
////        }
//
//
//    }

    /**
     * @param Request $request
     *
     * @throws Exception
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        Messages::error($this->getFailedLoginMessage());

        throw new Exception;
    }
}
