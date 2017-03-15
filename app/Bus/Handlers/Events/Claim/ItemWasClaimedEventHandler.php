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
        $listedItem = $claim->listedItem;
        $claimedBy = $claim->claimer;
        $seller = $listedItem->owner;

        try {

            $claimerEmail = $claimedBy->email;

            if ($claimedBy->primaryEmail && ($claimedBy->primaryEmail->isVerified() || $claimedBy->isGuest())) {
                with(new KitEmail('inventory.claims.emails.claimed_toclaimer', ['item' => $listedItem], function ($mailer) use ($claimerEmail) {
                    $mailer->to($claimerEmail)->subject('You claimed an item');
                }))->send();
            }

            if ($seller->checkIsNotifyable('inventory_claimed', 'email')) {
                if ($seller->primaryEmail && $seller->primaryEmail->isVerified()) {
                    $this->toEmail($seller, $listedItem);
                }
            }

            $this->toDatabase($seller, $claim, $listedItem);

        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }
    }

    /**
     * @param User $seller
     * @param ListableInterface $listedItem
     */
    public function toEmail(User $seller, ListableInterface $listedItem)
    {
        $email = new KitEmail;
        $email->setView('inventory.claims.emails.claimed_toseller')
            ->setParameters([
                'item' => $listedItem
            ])
            ->setCallable(function ($mailer) use ($seller) {
                $mailer->to($seller->primaryEmail->address)->subject('Item claimed.');
            })
            ->send();
    }

    /**
     * @param User      $user
     * @param Claims    $claim
     * @param ListableInterface $listedItem
     */
    public function toWeb(User $user, Claims $claim, ListableInterface $listedItem)
    {
        $pusher = new WebSocket;
        $pusher->setChannelName('private.'.env('APP_ENV').'.claims.'.$user->id)
            ->setEventName('item:claimed')
            ->setPayload([
                'id' => $claim->id,
                'inventory_item_id' => $listedItem->id
            ])
            ->send();
    }

    /**
     * @param User      $user
     * @param Claims    $claim
     * @param ListableInterface $listedItem
     */
    public function toDatabase(User $user, Claims $claim, ListableInterface $listedItem)
    {
        $title = $listedItem->getTitle().' was claimed by '. $claim->claimer->username;

        $notification = new NotificationNotices;
        $notification->user_id = $user->id;
        $notification->notification_id = null;
        $notification->reference_id = $claim->id;
        $notification->reference_type = get_class($claim);
        $notification->payload = '';
        $notification->title = $title;
        $notification->description = '';
        $notification->reference_url = route('shop.claims.index', [$user->username]);
        $notification->save();
    }

    /**
     * @param       $facebookPostId
     * @param array $params
     * @param       $userToken
     */
    public function handleFacebookCommentToPhoto($facebookPostId, array $params, $userToken)
    {
        $this->facebook->postCommentToPhoto($facebookPostId, $params, $userToken);
    }
}
