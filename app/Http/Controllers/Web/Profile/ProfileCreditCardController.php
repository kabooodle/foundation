<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Profile;

use Binput;
use Kabooodle\Models\Traits\CreditCardTrait;
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
    use CreditCardTrait;

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $user = webUser();
        $card = webUser()->getCard();

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
            $this->validate($request, $this->getCardRules());

            $this->dispatchNow(new StoreCreditCardForUserCommand(
                webUser(),
                Binput::get('card_number'),
                Binput::get('exp_month'),
                Binput::get('exp_year'),
                Binput::get('cvv')
            ));

            Messages::success('Credit card successfully saved.');

            return redirect()->route('profile.creditcard.index');
        } catch (ValidationException $e) {
            Messages::error('Some fields require input!');

            return redirect()->route('profile.creditcard.index')->withInput($request->all())->withErrors($e->validator);
        } catch (Card $e) {
            Messages::error($e->getMessage());

            return redirect()->route('profile.creditcard.index')->withInput($request->all());
        }
    }
}
