<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\User;

use Illuminate\Contracts\Mail\Mailer;
use Kabooodle\Bus\Events\User\UserWasCreatedEvent;

/**
 * Class UserWasCreatedListener
 * @package Kabooodle\Bus\Handlers\Events\User
 */
class UserWasCreatedListener
{
    /**
     * @var Mailer
     */
    public $mailer;

    /**
     * UserWasCreatedListener constructor.
     *
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param UserWasCreatedEvent $event
     */
    public function handle(UserWasCreatedEvent $event)
    {
        $user = $event->getUser();
        $this->mailer->send('auth.emails.welcome', ['user' => $user], function ($m) use ($user) {
            $m->to($user->email)->subject('Welcome to '.env('APP_NAME').'!');
        });

//        $this->nexmo->message()->send([
//            'to' => '19163902455',
//            'from' => '12242140596',
//            'text' => 'Welcome to invoSales! Check your email.'
//        ]);
    }
}