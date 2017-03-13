<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Subscription;

use Bugsnag;
use Exception;
use Kabooodle\Models\User;
use Kabooodle\Libraries\Emails\PiperEmail;
use Kabooodle\Bus\Events\Subscriptions\SubscriptionCancelled;

/**
 * Class NotifyUserSubscriptionCancelled
 */
class NotifyUserSubscriptionCancelled
{
    /**
     * @var string
     */
    protected $subject = 'Subscription cancelled';

    /**
     * @param SubscriptionCancelled $event
     */
    public function handle(SubscriptionCancelled $event)
    {
        $user = $event->getUser();
        try {
            $this->toEmail($user, $user->primaryEmail->address);
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }
    }

    /**
     * @param User   $user
     * @param string $emailAddress
     */
    public function toEmail(User $user, string $emailAddress)
    {
        $subject = $this->subject;
        $email = new PiperEmail;
        $email->setView('subscription.emails.cancelled')
            ->setParameters([
                'user' => $user
            ])
            ->setCallable(function ($m) use ($emailAddress, $subject) {
                $m->to($emailAddress)
                    ->subject($subject);
            })
            ->send();
    }
}