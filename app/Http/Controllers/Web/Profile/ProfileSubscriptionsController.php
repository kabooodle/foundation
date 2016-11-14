<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Profile;

use Binput;
use Messages;
use Response;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Bus\Commands\Credits\SubscribeUserToPlanCommand;
use Kabooodle\Foundation\Exceptions\Subscription\UserAlreadySubscribedToPlanException;

/**
 * Class ProfileSubscriptionsController
 * @package Kabooodle\Http\Controllers\Web\Profile
 */
class ProfileSubscriptionsController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $user = user();

        return $this->view('profile.subscription.index')->with(compact('user'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, $this->rules(), $this->rulesMessages());

            $plan = Binput::get('p', false);
            $user = user();

            if (! $user->hasCardOnFile()) {
                Messages::error('No credit card on file.');

                return $this->redirect()->route('profile.creditcard.index');
            }

            $this->dispatchNow(new SubscribeUserToPlanCommand($user, 'main', $plan, false));

            Messages::success('Congratulations! Your subscription was activated!');

            return redirect('/');
        } catch (ValidationException $e) {
            Messages::error($e->validator->messages()->first());

            return redirect()->back()->withInput()->withErrors($e->validator);
        } catch (UserAlreadySubscribedToPlanException $e) {
            Messages::error("You're already subscribed to this plan!");

            return redirect(route('profile.subscription.index'));
        } catch (\Exception $e) {
            dd($e);
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        try {
            user()->subscription('main')->cancelNow();

            return Response::json(['onGracePeriod' => user()->subscription('main')->onGracePeriod(), 'onTrial' => user()->subscription('main')->onTrial()], 200);
        } catch (Exception $e) {
            return Response::json([], 500);
        }
    }

    /**
     * @return array
     */
    private function rules()
    {
        return  [
            'p' => 'required|in:kabooodle_launch_plan'
        ];
    }

    /**
     * @return array
     */
    private function rulesMessages()
    {
        return [
            'p.in' => 'Plan you selected is not available.'
        ];
    }
}
