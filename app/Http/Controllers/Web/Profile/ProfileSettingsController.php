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
            'username' => 'required|alpha_dash|min:5|max:30|unique:users,username,' . user()->id,
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
        $data = [
            'fromAddresses' => user()->shipFromAddresses,
            'primaryFrom' => user()->primaryShipFromAddress,
            'toAddresses' => user()->shipToAddresses,
            'primaryTo' => user()->primaryShipToAddress,
        ];

        return $this->view('profile.shippingprofile', $data);
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
        $data = [
            'primaryEmail' => user()->primaryEmail,
            'emails' => user()->emails
        ];

        return $this->view('profile.emails', $data);
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
            return $this->redirect()->route('profile.emails.index');
        }

        return $this->redirect()->route('auth.login');
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function getNotifications()
    {
        $notifications = $this->dispatchNow(new GetActiveNotifications);
        $notifications = $notifications->groupBy('group');

        $userNotifications = user()->notificationsettings;

        return $this->view('profile.notifications')->with(compact('notifications', 'userNotifications'));
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
