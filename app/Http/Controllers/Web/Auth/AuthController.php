<?php

namespace Kabooodle\Http\Controllers\Web\Auth;

use Auth;
use Messages;
use Validator;
use Kabooodle\Models\User;
use Illuminate\Http\Request;
use Kabooodle\Bus\Events\User\UserLoggedInEvent;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Bus\Commands\User\AddUserCommand;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
    protected $redirectTo = '/';

    /**
     * @var string
     */
    protected $redirectAfterLogout = '/';

    /**
     * @return void
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
            $this->validate($request, User::getRules(), ['email.unique' => 'Email address is unavailable.']);

            $user = $this->dispatch(new AddUserCommand($request->get('name'), $request->get('email'), $request->get('password')));

            Auth::attempt([
                'email' => $user->email,
                'password' => $request->get('password')
            ]);

            Messages::success("Welcome to ".env('APP_NAME').", {$user->name} !");

            return $this->redirect('/');

        } catch (\Illuminate\Validation\ValidationException $e) {

            Messages::error($e->validator->getMessageBag()->first());

            return $this->redirect(route('auth.register'))
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

            return $this->parentLogin($request);
        } catch (\Illuminate\Validation\ValidationException $e) {

            Messages::error($e->validator->getMessageBag()->first());

            return $this->redirect(route('auth.login'))
                ->withErrors($e->validator->getMessageBag());
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
