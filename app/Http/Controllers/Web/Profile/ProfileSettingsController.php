<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Profile;

use Binput;
use Kabooodle\Bus\Commands\Email\VerifyEmailCommand;
use Kabooodle\Bus\Commands\Notifications\UpdateUserNotificationSettingCommand;
use Response;
use Illuminate\Support\Facades\Hash;
use Kabooodle\Bus\Commands\Notifications\GetActiveNotifications;
use Kabooodle\Bus\Events\User\UserSettingsUpdated;
use Kabooodle\Libraries\Timezone;
use Kabooodle\Models\User;
use Messages;
use Illuminate\Http\Request;
use Kabooodle\Models\MailingAddress;
use Kabooodle\Models\Address;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Bus\Commands\User\UpdateUserShippingProfileCommand;
use PragmaRX\Support\DateTime;

/**
 * Class ProfileSettingsController
 * @package Kabooodle\Http\Controllers\Web\Profile
 */
class ProfileSettingsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $user = user();
        $timezone = Timezone::timezoneList();

        return $this->view('profile.index')->with(compact('user', 'timezone'));
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postProfile(Request $request)
    {
        $input = [
            'first_name' => Binput::get('first_name'),
            'last_name' => Binput::get('last_name'),
            'username' => Binput::get('username'),
            'password' => Binput::get('password'),
            'newPassword' => Binput::get('newPassword'),
            'newPassword_confirmation' => Binput::get('newPassword_confirmation'),
            'timezone' => Binput::get('timezone')
        ];

        // Set Validation Rules
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required|alpha_dash|unique:users,username,' . user()->id,
            'password' => 'required_with:newPassword,newPassword_confirmation',
            'newPassword' => 'required_with:newPassword_confirmation,password|min:6|confirmed',
            'newPassword_confirmation' => 'required_with:newPassword',
            'timezone' => 'required'
        ];

        try {
            $this->validate($request, $rules);

            user()->first_name = $input['first_name'];
            user()->last_name = $input['last_name'];
            user()->username = $input['username'];
            // TODO: profile updates email
//            user()->email = $input['email'];
            user()->timezone = $input['timezone'];
            user()->avatar = $request->has('avatar') ? $request->get('avatar') : null;

            if ($input['newPassword']) {
                if (!Hash::check($input['password'], user()->password)) {
                    Messages::error('Password is incorrect.');

                    return $this->redirect(route('profile.index'));
                }
                $password = Hash::make($input['newPassword']);
                user()->password = $password;
            }

            user()->save();

            event(new UserSettingsUpdated(user()));

            Messages::success("Profile updated!");

            return $this->redirect()->route('profile.index');
        } catch (ValidationException $e) {
            Messages::error($e->validator->getMessageBag()->first());

            return $this->redirect(route('profile.index'))
                ->withErrors($e->validator->getMessageBag());
        }
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function getShippingProfile()
    {
        $from = user()->primaryShipFromAddress;
        $to = user()->primaryShipToAddress;

        return $this->view('profile.shippingprofile')->with(compact('from', 'to'));
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function postShippingProfile(Request $request)
    {
        try {
            $this->validate($request, Address::getRules());

            $kabooodleAsDefaultShippingProvider = $request->has('kabooodle_as_shipping') ? true : false;

            $fromAddressArray = Binput::get('from');
            $toAddressArray = Binput::get('to');

            $from = new MailingAddress(
                $fromAddressArray['company'],
                $fromAddressArray['street1'],
                array_get($fromAddressArray, 'street2'),
                $fromAddressArray['city'],
                $fromAddressArray['state'],
                $fromAddressArray['zip'],
                array_get($fromAddressArray, 'email'),
                array_get($fromAddressArray, 'phone')
            );

            $to = new MailingAddress(
                array_get($toAddressArray, 'company'),
                array_get($toAddressArray, 'street1'),
                array_get($toAddressArray, 'street2'),
                array_get($toAddressArray, 'city'),
                array_get($toAddressArray, 'state'),
                array_get($toAddressArray, 'zip'),
                array_get($toAddressArray, 'email'),
                array_get($toAddressArray, 'phone')
            );

            $this->dispatchNow(new UpdateUserShippingProfileCommand(user(), $from, $to, $kabooodleAsDefaultShippingProvider));

            Messages::success("Shipping profile was successfully updated!");

            return $this->redirect()->route('profile.shippingprofile.edit');
        } catch (ValidationException $e) {
            Messages::error('Some fields require input!');

            return $this->redirect(route('profile.shippingprofile.edit'))
                ->withErrors($e->validator->getMessageBag());
        }
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function getSocial()
    {
        return $this->view('profile.social');
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function getEmails()
    {
        $emails = user()->emails;

        return $this->view('profile.emails')->with(compact('emails'));
    }

    /**
     * @param Request $request
     * @param         $token
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyEmail(Request $request, $token)
    {
        $this->dispatchNow(new VerifyEmailCommand($token));

        return $this->redirect()->route('emails.verified');
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function verifiedEmail(Request $request)
    {
        Messages::success("Email successfully verified!");

        if (user()) {
            return $this->view('profile.email-verified');
        }

        return $this->redirect()->route('auth.login');
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function getNotifications()
    {
        $notifications = $this->dispatchNow(new GetActiveNotifications);

        return $this->view('profile.notifications')->with(compact('notifications'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postNotifications(Request $request)
    {
        try {
            $this->validate($request, $this->getNotificationRules());

            $this->dispatchNow(new UpdateUserNotificationSettingCommand(
                user(),
                Binput::get('id'),
                Binput::get('type'),
                Binput::get('action')
            ));

            return Response::json(null, 200);
        } catch (ValidationException $e) {
            return Response::json($e->getMessage(), 500);
        }
    }

    /**
     * @return array
     */
    public function getNotificationRules()
    {
        $notifications = $this->dispatchNow(new GetActiveNotifications);

        return [
            'id' => 'required|in:'.implode(',', $notifications->pluck('id')->toArray()),
            'action' => 'required|in:subscribed,unsubscribed',
            'type' => 'required|in:web,email'
        ];
    }
}
