<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Claim;

use Bugsnag;
use Exception;
use Kabooodle\Models\Contracts\ListableInterface;
use Kabooodle\Models\User;
use Kabooodle\Models\Claims;
use Kabooodle\Models\Inventory;
use Kabooodle\Models\FacebookItems;
use Kabooodle\Libraries\Emails\KitEmail;
use Illuminate\Contracts\Mail\MailQueue;
use Kabooodle\Models\NotificationNotices;
use Kabooodle\Libraries\WebSockets\WebSocket;
use Kabooodle\Bus\Events\Claim\NewItemWasClaimedEvent;
use Kabooodle\Services\Social\Facebook\FacebookSdkService;

/**
 * Class ItemWasClaimedEventHandler
 * @package Kabooodle\Bus\Handlers\Events\Claim
 */
class ItemWasClaimedEventHandler
{
    /**
     * @var MailQueue
     */
    protected $mailer;

    /**
     * @var FacebookSdkService
     */
    protected $facebook;

    /**
     * ItemWasClaimedEventHandler constructor.
     *
     * @param MailQueue          $mailer
     * @param FacebookSdkService $facebookSdkService
     */
    public function __construct(MailQueue $mailer, FacebookSdkService $facebookSdkService)
    {
        $this->mailer = $mailer;
        $this->facebook = $facebookSdkService;
    }

    /**
     * @param NewItemWasClaimedEvent $event
     */
    public function handle(NewItemWasClaimedEvent $event)
    {
        // We need to email two people, the seller and the person who claimed the item.
        $claim = $event->getclaim();
        /** @var ListableInterface $listedItem */
        $listedItem = $claim->listingItem;
        $claimedBy = $claim->claimer;
        $seller = $listedItem->owner;

        try {

            if ($claimedBy->primaryEmail && ($claimedBy->primaryEmail->isVerified() || $claimedBy->isGuest())) {
                $this->toClaimerEmail($seller, $claim, $claimedBy);
            }

            if ($seller->checkIsNotifyable('inventory_claimed', 'email')) {
                if ($seller->primaryEmail && $seller->primaryEmail->isVerified()) {
                    $this->toEmail($seller, $claim, $listedItem);
                }
            }

            $this->toDatabase($seller, $claim);

        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }
    }

    /**
     * @param User              $seller
     * @param Claims            $claim
     * @param ListableInterface $listedItem
     */
    public function toEmail(User $seller, Claims $claim, $listedItem)
    {
        $email = new KitEmail;
        $subject = $claim->listable->getTitle().' was claimed.';
        $emailAddress = $seller->primaryEmail->address;
        $email->setView('claims.emails.claimed_toseller')
            ->setParameters([
                'item' => $listedItem,
                'claim' => $claim,
                'seller' => $seller,
            ])
            ->setCallable(function ($mailer) use ($emailAddress, $subject) {
                $mailer->to($emailAddress)->subject($subject);
            })
            ->send();
    }

    /**
     * @param User   $seller
     * @param Claims $claim
     * @param User   $claimer
     */
    public function toClaimerEmail(User $seller, Claims $claim, User $claimer)
    {
        $email = new KitEmail;
        $subject = 'You claimed the '.$claim->listable->getTitle();
        $emailAddress = $claimer->email;
        $email->setView('claims.emails.claimed_toclaimer')
            ->setParameters([
                'item' => $claim->listingItem,
                'claim' => $claim,
                'seller' => $seller,
                'claimer' => $claimer
            ])
            ->setCallable(function ($mailer) use ($emailAddress, $subject) {
                $mailer->to($emailAddress)->subject($subject);
            })
            ->send();
    }

    /**
     * @param User      $user
     * @param Claims    $claim
     */
    public function toDatabase(User $user, Claims $claim)
    {
        $title = $claim->listable->getTitle().' was claimed by '. $claim->claimer->username;

        $notification = new NotificationNotices;
        $notification->user_id = $user->id;
        $notification->notification_id = null;
        $notification->reference_id = $claim->id;
        $notification->reference_type = get_class($claim);
        $notification->payload = '';
        $notification->title = $title;
        $notification->description = '';
        $notification->reference_url = route('claims.index');
        $notification->save();
    }
}
