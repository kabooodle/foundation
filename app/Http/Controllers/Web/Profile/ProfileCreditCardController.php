<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Profile;

use Binput;
use Messages;
use Stripe\Error\Card;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Bus\Commands\Credits\StoreCreditCardForUserCommand;


/**
 * Class ProfileCreditCardController
 * @package Kabooodle\Http\Controllers\Web\Profile
 */
class ProfileCreditCardController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $user = user();
        $card = user()->getCard();

        return $this->view('profile.creditcard.index')->with(compact('user', 'card'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, $this->rules());

            $this->dispatchNow(new StoreCreditCardForUserCommand(
                user(),
                Binput::get('card_number'),
                Binput::get('exp_month'),
                Binput::get('exp_year'),
                Binput::get('cvv')
            ));

            Messages::success('Credit card successfully saved.');

            return redirect()->route('profile.creditcard.index');

        } catch (ValidationException $e) {
            Messages::error('Some fields require input!');

            return redirect()->back()->withInput($request->all())->withErrors($e->validator);
        } catch (Card $e) {
            Messages::error($e->getMessage());

            return redirect()->route('profile.creditcard.index')->withInput($request->all());
        }
    }

    /**
     * @return array
     */
    private function rules()
    {
        return [
            'card_number' => 'required',
            'exp_month' => 'required',
            'exp_year' => 'required',
            'cvv' => 'required'
        ];
    }
}