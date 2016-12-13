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
        // and send an email to the referer notifying them.
        /** @var User $referer */
        if ($referer = $this->checkIfUserWasReferred($user)) {
            if ($referer->checkIsNotifyable('referral_joined', 'web')) {
                if ($referer->primaryEmail->isVerified()) {
                    $this->notifyReferer($user, $referer);
                }
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
     * @param $referer
     */
    public function notifyReferer($user, $referer)
    {
        $mail = new PiperEmail;
        $mail->setView('auth.emails.referraljoined')
            ->setParameters(['user' => $user, 'referee' => $referer])
            ->setCallable(function ($m) use ($user, $referer) {
                $m->to($referer->email)
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
