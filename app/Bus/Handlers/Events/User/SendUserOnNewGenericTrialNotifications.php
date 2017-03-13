<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\User;

use Kabooodle\Libraries\Emails\PiperEmail;
use Kabooodle\Bus\Events\User\UserUpgradedToGenericTrial;

/**
 * Class SendUserOnNewGenericTrialNotifications
 */
class SendUserOnNewGenericTrialNotifications
{
    /**
     * @param UserUpgradedToGenericTrial $event
     */
    public function handle(UserUpgradedToGenericTrial $event)
    {
        $user = $event->getUser();

        // Send welcome email to user.
        $this->sendWelcomeEmail($user);
    }

    /**
     * @param $user
     */
    public function sendWelcomeEmail($user)
    {
        $mail = new PiperEmail;
        $mail->setView('profile.subscription.emails.started_generic_trial')
            ->setParameters(['user' => $user])
            ->setCallable(function ($m) use ($user) {
                $m->to($user->email)
                    ->subject('Your Merchant Plus Trial has started!');
            })
            ->send();
    }
}
