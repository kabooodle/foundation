<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Profile;

use Binput;
use Messages;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Bus\Commands\Credits\PurchaseCreditsForUserCommand;

/**
 * Class ProfileCreditsController
 * @package Kabooodle\Http\Controllers\Web\Profile
 */
class ProfileCreditsController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $user = user();

        return $this->view('profile.credits.index')->with(compact('user'));
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

            $creditTypeId = Binput::get('p', false);
            $user = user();

            if (! $user->hasCardOnFile()) {
                Messages::error('No credit card on file.');

                return $this->view('profile.creditcard.index');
            }

            $this->dispatchNow(new PurchaseCreditsForUserCommand($user, $creditTypeId));

            Messages::success('Congratulations! Credits added to your account');

            return redirect()->route('profile.credits.index');
        } catch (ValidationException $e) {
            Messages::error($e->validator->messages()->first());

            return redirect()->back()->withInput()->withErrors($e->validator);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    /**
     * @return array
     */
    private function rules()
    {
        return  [
            'p' => 'required|in:'.implode(',',creditTypes()->pluck('id')->toArray())
        ];
    }

    /**
     * @return array
     */
    private function rulesMessages()
    {
        return [
            'p.in' => 'Credits group you selected is not available.'
        ];
    }
}