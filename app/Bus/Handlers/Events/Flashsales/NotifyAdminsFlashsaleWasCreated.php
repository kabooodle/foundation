<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Flashsales;

use Bugsnag;
use Exception;
use Kabooodle\Models\User;
use Illuminate\Bus\Queueable;
use Kabooodle\Models\FlashSales;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Kabooodle\Models\NotificationNotices;
use Kabooodle\Libraries\Emails\PiperEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kabooodle\Bus\Events\Flashsale\FlashsaleWasCreatedEvent;

/**
 * Class NotifyAdminsFlashsaleWasCreated
 */
class NotifyAdminsFlashsaleWasCreated implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    public $subject;

    /**
     * @param FlashsaleWasCreatedEvent $event
     */
    public function handle(FlashsaleWasCreatedEvent $event)
    {
        try {
            /** @var FlashSales $flashsale */
            $flashsale = FlashSales::with(['sellerGroups', 'admins'])->findOrFail($event->getFlashsaleId());
            $admins = $flashsale->admins;
            $owner = $flashsale->owner;

            $this->subject = 'You were added as an admin to the flash sale ' . $flashsale->name . ' by ' . $owner->username;
            foreach ($admins as $admin) {
                // dont need to do anything with ourselves.
                if ($admin->id == $owner->id) {
                    continue;
                }

                if ($admin->checkIsNotifyable('flashsale_invited_as_admin', 'email')) {
                    if ($admin->primaryEmail && $admin->primaryEmail->isVerified()) {
                        $this->toEmail($owner, $admin, $flashsale);
                    }
                }

                $this->toDatabase($owner, $admin, $flashsale);
            }
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }
    }

    /**
     * @param User       $owner
     * @param User       $admin
     * @param FlashSales $flashsale
     */
    public function toEmail(User $owner, User $admin, FlashSales $flashsale)
    {
        $subject = $this->subject;
        $emailAddress = $admin->primaryEmail->address;
        $email = new PiperEmail;
        $email->setView('flashsales.emails._createdtoadmin')
            ->setParameters([
                'owner' => $owner,
                'flashsale' => $flashsale,
                'daterange' => $flashsale->present()->getDateRange()
            ])
            ->setCallable(function ($mailer) use ($emailAddress, $subject) {
                $mailer->to($emailAddress)->subject($subject);
            })
            ->send();
    }

    /**
     * @param User       $owner
     * @param User       $admin
     * @param FlashSales $flashsale
     */
    public function toDatabase(User $owner, User $admin, FlashSales $flashsale)
    {
        $notification = new NotificationNotices;
        $notification->user_id = $admin->id;
        $notification->notification_id = null;
        $notification->reference_id = $flashsale->id;
        $notification->reference_type = get_class($flashsale);
        $notification->reference_url = route('flashsales.show', [$flashsale->uuid]);
        $notification->payload = '';
        $notification->title = $this->subject;
        $notification->description = '';
        $notification->save();
    }
}
