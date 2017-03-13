<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Profile;

use Binput;
use Laravel\Cashier\Subscription;
use Messages;
use Response;
use Exception;
use Stripe\Error\Card;
use Kabooodle\Models\Plans;
use Illuminate\Http\Request;
use Kabooodle\Models\Traits\CreditCardTrait;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Bus\Events\Profile\UserCancelledSubscriptionEvent;
use Kabooodle\Bus\Commands\Credits\StoreCreditCardForUserCommand;
use Kabooodle\Bus\Commands\Subscriptions\SubscribeUserToPlanCommand;
use Kabooodle\Foundation\Exceptions\Subscription\UserHasNoCreditCardOnFileException;
use Kabooodle\Foundation\Exceptions\Subscription\UserAlreadySubscribedToPlanException;

/**
 * Class ProfileSubscriptionsController
 * @package Kabooodle\Http\Controllers\Web\Profile
 */
class ProfileSubscriptionsController extends Controller
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
        $subscription = webUser()->currentSubscription();

        return $this->view('profile.subscription.index')->with(compact('user', 'subscription'));
    }

    /**
     * @param Request $request
     * @param sting   $planId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show(Request $request, $planId)
    {
        $planId = Binput::clean($planId);
        list($plan, $planGroup) = Plans::getPlan($planId);

        // Check if the plan is an early adopter one and the user is allowed to use this plan
        if ($planId === Plans::PLAN_EARLY_ADOPTER && ! webUser()->isEarlyAdapter()) {
            Messages::error('Invalid Plan Selected.');

            return redirect()->route('profile.subscription.index');
        }

        $card = webUser()->getCard();
        $subscription = webUser()->currentSubscription();

        return $this->view('profile.subscription.show')->with(compact('card', 'subscription', 'planId', 'planGroup', 'plan'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Request $request)
    {
        /** @var Subscription $currentSubscription */
        $currentSubscription = webUser()->currentSubscription();
        if (!$currentSubscription || $currentSubscription->cancelled()) {
            return redirect()->route('profile.subscription.index');
        }

        return $this->view('profile.subscription.edit');
    }

    /**
     * @param Request $request
     * @param $planId
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request, $planId)
    {
        try {
            $user = webUser();
            $planId = Binput::clean($planId);
            list($plan, $planGroup) = Plans::getPlan($planId);

            // Check if the plan is an early adopter one and the user is allowed to use this plan
            if ($planId === Plans::PLAN_EARLY_ADOPTER && ! webUser()->isEarlyAdapter()) {
                Messages::error('Invalid Plan Selected.');

                return redirect()->route('profile.subscription.index');
            }

            // We are adding a new card.
            if ($request->has('newcard')) {
                $this->validate($request, $this->getCardRules());

                $this->dispatchNow(new StoreCreditCardForUserCommand(
                    webUser(),
                    Binput::get('card_number'),
                    Binput::get('exp_month'),
                    Binput::get('exp_year'),
                    Binput::get('cvv')
                ));

                $user = $user->fresh();
            }

            if (!$user->hasCardOnFile()) {
                Messages::error('No credit card on file.');

                return $this->redirect()->route('profile.creditcard.index');
            }
            
            $this->dispatchNow(new SubscribeUserToPlanCommand($user, $planGroup['subscription'], $plan['id'], true, 0));

            Messages::success('Congratulations! Your subscription was activated!');

            return redirect()->route('user.profile', [$user->username]);
        } catch (ValidationException $e) {
            Messages::error($e->validator->messages()->first());

            return redirect()->back()->withInput()->withErrors($e->validator);
        } catch (UserAlreadySubscribedToPlanException $e) {
            Messages::error("You're already subscribed to this plan!");

            return redirect(route('profile.subscription.index'));
        } catch (UserHasNoCreditCardOnFileException $e) {
            return redirect()->route('profile.creditcard.index');
        } catch (Card $e) {
            Messages::error($e->getMessage());

            return redirect()->back()->withInput($request->all());
        } catch (\Exception $e) {
            dd($e);
            return redirect(route('profile.subscription.index'));
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        try {
            /** @var Subscription $currentSubscription */
            $currentSubscription = webUser()->currentSubscription();
            if (!$currentSubscription || $currentSubscription->cancelled()) {
               throw new Exception;
            }
            $currentSubscription->cancel();

            event(new UserCancelledSubscriptionEvent(webUser(), $currentSubscription->name));

            Messages::add('success', 'Subscription has been cancelled and will not be renewed.');

            return $this->redirect()->route('profile.subscription.index');
        } catch (Exception $e) {
            return $this->redirect()->route('profile.subscription.index');
        }
    }
}
