<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\User;

use Kabooodle\Models\User;
use Kabooodle\Libraries\Emails\PiperEmail;
use Kabooodle\Bus\Events\User\UserWasCreatedEvent;

/**
 * Class UserWasCreatedListener
 * @package Kabooodle\Bus\Handlers\Events\User
 */
class UserWasCreatedListener
{
    /**
     * @param UserWasCreatedEvent $event
     */
    public function handle(UserWasCreatedEvent $event)
    {
        $user = $event->getUser();

        // Send welcome email to user.
        $this->sendWelcomeEmail($user);

        // Check if user was referred by someone
        // and send an email to the referee notifying them.
        /** @var User $referee */
        if ($referee = $this->checkIfUserWasReferred($user)) {
            if ($referee->checkIsNotifyable('referral_joined', 'web')) {
                $this->notifyReferee($user, $referee);
            }
        }
    }

    /**
     * @param $user
     */
    public function sendWelcomeEmail($user)
    {
        $mail = new PiperEmail;
        $mail->setView('auth.emails.welcome')
            ->setParameters(['user' => $user])
            ->setCallable(function ($m) use ($user) {
                $m->to($user->email)
                    ->subject('Welcome to '.env('APP_NAME').'!');
            })
            ->send();
    }

    /**
     * @param $user
     * @param $referee
     */
    public function notifyReferee($user, $referee)
    {
        $mail = new PiperEmail;
        $mail->setView('auth.emails.referraljoined')
            ->setParameters(['user' => $user, 'referee' => $referee])
            ->setCallable(function ($m) use ($user, $referee) {
                $m->to($referee->email)
                    ->subject(env('APP_NAME').' referral joined!');
            })
            ->send();
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function checkIfUserWasReferred(User $user)
    {
        return $user->referredBy;
    }
}
