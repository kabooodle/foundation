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
 * Class NotifySellersFlashsaleWasCreated
 */
class NotifySellersFlashsaleWasCreated implements ShouldQueue
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
            $sellers = $flashsale->sellers();
            $owner = $flashsale->owner;

            $this->subject = 'You were added as a seller to the flash sale ' . $flashsale->name . ' by ' . $owner->username;

            foreach ($sellers as $seller) {
                // dont need to do anything with ourselves.
                if ($seller->id == $owner->id) {
                    continue;
                }

                $isAdmin = $flashsale->canSellerListToFlashsaleAnytime($seller->id);
                if ($isAdmin) {
                    $timeslot = null;
                } else {
                    $sellerGroup = $flashsale->getFlashsaleSellerGroupForUser($seller->id);
                    $timeslot = $sellerGroup->pivot->time_slot;
                }

                if ($seller->checkIsNotifyable('flashsale_invited_as_seller', 'email')) {
                    if ($seller->primaryEmail && $seller->primaryEmail->isVerified()) {
                        $this->toEmail($owner, $seller, $flashsale, $timeslot);
                    }
                }

                $this->toDatabase($owner, $seller, $flashsale);
            }
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }
    }

    /**
     * @param User       $owner
     * @param User       $seller
     * @param FlashSales $flashsale
     * @param null       $timeslot
     */
    public function toEmail(User $owner, User $seller, FlashSales $flashsale, $timeslot = null)
    {
        $subject = $this->subject;
        $emailAddress = $seller->primaryEmail->address;
        $email = new PiperEmail;
        $email->setView('flashsales.emails._createdtoseller')
            ->setParameters([
                'owner' => $owner,
                'flashsale' => $flashsale,
                'timeslot' => $timeslot,
                'daterange' => $flashsale->present()->getDateRange()
            ])
            ->setCallable(function ($mailer) use ($emailAddress, $subject) {
                $mailer->to($emailAddress)->subject($subject);
            })
            ->send();
    }

    /**
     * @param User       $owner
     * @param User       $seller
     * @param FlashSales $flashsale
     */
    public function toDatabase(User $owner, User $seller, FlashSales $flashsale)
    {
        $notification = new NotificationNotices;
        $notification->user_id = $seller->id;
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
