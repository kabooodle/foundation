<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\User;

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

        $mail = new PiperEmail;
        $mail->setView('auth.emails.welcome')
            ->setParameters(['user' => $user])
            ->setCallable(function ($m) use ($user) {
                $m->to($user->email)
                    ->subject('Welcome to '.env('APP_NAME').'!');
            })
            ->send();

//        $this->nexmo->message()->send([
//            'to' => '19163902455',
//            'from' => '12242140596',
//            'text' => 'Welcome to invoSales! Check your email.'
//        ]);
    }
}