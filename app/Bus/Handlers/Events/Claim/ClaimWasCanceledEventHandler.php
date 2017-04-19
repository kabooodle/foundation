<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Claim;

use Kabooodle\Models\NotificationNotices;
use Kabooodle\Models\User;
use Kabooodle\Models\Claims;
use Illuminate\Queue\InteractsWithQueue;
use Kabooodle\Libraries\Emails\PiperEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kabooodle\Bus\Events\Claim\ClaimWasCanceledEvent;

/**
 * Class ClaimWasCanceledEvent
 * @package Kabooodle\Bus\Events
 */
class ClaimWasCanceledEventHandler implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var Claims
     */
    public $claim;

    /**
     * @var User
     */
    public $claimer;

    /**
     * @var User
     */
    public $seller;

    /**
     * @param ClaimWasCanceledEvent $event
     */
    public function handle(ClaimWasCanceledEvent $event)
    {
        /** @var Claims $claim */
        $this->claim = $event->getClaim();
        $this->seller = $this->claim->listable->owner;
        $this->claimer = $this->claim->claimer;

        try {

            if ($this->claimer->primaryEmail && ($this->claimer->primaryEmail->isVerified() || $this->claimer->isGuest())) {
                $this->toClaimerEmail();
            }

            if ($this->seller->checkIsNotifyable('claim_canceled', 'email')) {
                if ($this->seller->primaryEmail && $this->seller->primaryEmail->isVerified()) {
                    $this->toSellerEmail();
                }
            }

            $this->toDatabase();

        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }
    }

    public function toClaimerEmail()
    {
        $claimer = $this->claimer;
        $subject = 'You have successfully canceled your claim of ' . $this->claim->listedItem->getTitle();
        $mail = new PiperEmail;
        $mail->setView('claims.emails.canceled_toclaimer')
            ->setParameters(['item' => $this->claim->listedItem, 'claim' => $this->claim, 'seller' => $this->seller])
            ->setCallable(function ($mail) use ($claimer, $subject) {
                $mail->to($claimer->email)->subject($subject);
            })
            ->send();
    }

    public function toSellerEmail()
    {
        $seller = $this->seller;
        $subject = $this->claimer->full_name . ' canceled their claim of ' . $this->claim->listedItem->getTitle();
        $mail = new PiperEmail;
        $mail->setView('claims.emails.canceled_toseller')
            ->setParameters(['item' => $this->claim->listedItem, 'claim' => $this->claim])
            ->setCallable(function ($mail) use ($seller, $subject) {
                $mail->to($seller->email)->subject($subject);
            })
            ->send();
    }

    public function toDatabase()
    {
        $notification = new NotificationNotices;
        $notification->user_id = $this->seller->id;
        $notification->notification_id = null;
        $notification->reference_id = $this->claim->id;
        $notification->reference_type = get_class($this->claim);
        $notification->payload = '';
        $notification->title = $this->claimer->full_name . ' canceled their claim of ' . $this->claim->listedItem->getTitle();
        $notification->description = '';
        $notification->reference_url = route('merchant.claims.index');
        $notification->save();
    }
}
