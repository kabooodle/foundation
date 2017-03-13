<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Profile;

use Binput;
use Kabooodle\Bus\Commands\Email\VerifyEmailCommand;
use Kabooodle\Bus\Commands\Notifications\UpdateUserNotificationSettingCommand;
use Kabooodle\Models\Files;
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
        $user = webUser();
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
            'timezone' => Binput::get('timezone'),
            'about_me' => Binput::get('about_me')
        ];

        // Set Validation Rules
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'avatar' =>'required',
            'username' => 'required|alpha_dash|min:5|max:30|unique:users,username,' . webUser()->id,
            'password' => 'required_with:newPassword,newPassword_confirmation',
            'newPassword' => 'required_with:newPassword_confirmation,password|min:6|confirmed',
            'newPassword_confirmation' => 'required_with:newPassword',
            'timezone' => 'required'
        ];

        try {
            $this->validate($request, $rules);

            webUser()->first_name = $input['first_name'];
            webUser()->last_name = $input['last_name'];
            webUser()->username = $input['username'];
            // TODO: profile updates email
//            webUser()->email = $input['email'];
            webUser()->timezone = $input['timezone'];
            $avatar = $request->has('avatar') ? $request->get('avatar') : null;

            if ($input['newPassword']) {
                if (!Hash::check($input['password'], webUser()->password)) {
                    Messages::error('Password is incorrect.');

                    return $this->redirect(route('profile.index'));
                }
                $password = Hash::make($input['newPassword']);
                webUser()->password = $password;
            }

            if ($avatar) {
                $avatar = json_decode($avatar, true);
                if (webUser()->avatar && webUser()->avatar->key <> $avatar['key']) {
                    webUser()->avatar()->delete();

                    $file = new Files;
                    $file->fileable_id = webUser()->id;
                    $file->fileable_type = User::class;
                    $file->bucket_name = $avatar['bucket_name'];
                    $file->location = $avatar['location'];
                    $file->key = $avatar['key'];
                    $file->save();

                    webUser()->avatar()->save($file);
                }
            }

            webUser()->about_me = $input['about_me'] ? nl2br($input['about_me']) : null;

            webUser()->save();

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
            'fromAddresses' => webUser()->shipFromAddresses,
            'primaryFrom' => webUser()->primaryShipFromAddress,
            'toAddresses' => webUser()->shipToAddresses,
            'primaryTo' => webUser()->primaryShipToAddress,
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


    public function postSocial()
    {
        $user = webUser();
        $user->social_instagram = trim(Binput::get('social_instagram', null));
        $user->social_twitter = trim(Binput::get('social_twitter', null));
        $user->social_youtube = trim(Binput::get('social_youtube', null));
        $user->social_website = trim(Binput::get('social_website', null));
        $user->save();

        Messages::success("Profile updated!");

        return redirect()->route('profile.social.edit');
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function getEmails()
    {
        $data = [
            'primaryEmail' => webUser()->primaryEmail,
            'emails' => webUser()->emails
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
        $notifications = $notifications->filter(function($notification){
            if ($notification->required_subscription_type == 'merchant' && ! webUser()->hasAtLeastMerchantSubscription()) {
                return false;
            }
            return $notification;
        })->sortBy('group')->groupBy('group');

        $userNotifications = webUser()->notificationsettings;

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
                webUser(),
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
            'type' => 'required|in:web,email,sms'
        ];
    }
}
