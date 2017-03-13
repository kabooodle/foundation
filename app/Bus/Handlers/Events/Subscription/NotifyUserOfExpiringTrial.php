<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Subscription;

use Bugsnag;
use Exception;
use Kabooodle\Models\User;
use Kabooodle\Services\User\UserService;
use Kabooodle\Models\NotificationNotices;
use Kabooodle\Libraries\Emails\PiperEmail;
use Kabooodle\Bus\Events\Subscriptions\TrialAccountExpiring;

/**
 * Class NotifyUserOfExpiringTrial
 */
class NotifyUserOfExpiringTrial
{
    /**
     * @var UserService
     */
    static $userService;

    /**
     * @var string
     */
    protected $subject = 'Kabooodle trial account expiring soon';

    /**
     * @param TrialAccountExpiring $event
     */
    public function handle(TrialAccountExpiring $event)
    {
        $user = $event->getAccountHolder();
        try {
            $this->toEmail($user, $user->primaryEmail->address, $event->getDaysInterval());
            $this->toDatabase($user);
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }
    }

    /**
     * @param User $user
     * @param string $emailAddress
     */
    public function toEmail(User $user, string $emailAddress, int $expiresInterval)
    {
        $subject = $this->subject;
        $email = new PiperEmail;
        $email->setView('subscription.emails.trialexpiring')
            ->setParameters([
                'user' => $user,
                'interval' => $expiresInterval
            ])
            ->setCallable(function ($m) use ($emailAddress, $subject) {
                $m->to($emailAddress)
                    ->subject($subject);
            })
            ->send();
    }

    /**
     * @param User $user
     */
    public function toDatabase(User $user)
    {
        $notification = new NotificationNotices;
        $notification->user_id = $user->id;
        $notification->notification_id = null;
        $notification->reference_id = '';
        $notification->reference_type = '';
        $notification->reference_url = route('profile.subscription.index');
        $notification->payload = '';
        $notification->title = $this->subject;
        $notification->description = '';
        $notification->save();
    }

    /**
     * @return \Illuminate\Foundation\Application|mixed
     */
    public function getUserService()
    {
        if (!self::$userService) {
            self::$userService = app()->make(UserService::class);
        }

        return self::$userService;
    }
}
