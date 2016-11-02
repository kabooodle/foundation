<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Claim;

use Exception;
use Kabooodle\Models\FacebookItems;
use Kabooodle\Libraries\Emails\KitEmail;
use Illuminate\Contracts\Mail\MailQueue;
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
        $claimedBy = $event->getclaim()->claimedBy;
        $seller = $event->getclaim()->inventoryItem->owner;
        $availableQty = $event->getclaim()->inventoryItem->getAvailableQuantity();
        $shoppable = $event->getclaim()->shoppable;

        // If a claimed item was claimed via facebook, we need to handle any business logic
        // Currently, there is only one rule: Create a FB request, adding a "Sold" comment to photo.
        if ($event->getclaim()->shoppable_type == FacebookItems::class) {
            try {
                $this->handleFacebookCommentToPhoto($shoppable->facebook_post_id, ['message' => 'Sold'], $seller->getFacebookUserToken());
            } catch (Exception $e) {
                // event()
            }
        }

        // 2nd business logic requires that we count the number of facebook albums this item has been posted to
        // and if the item is out of stock, we need to post claimed to all the remaining sales as well.
        if ($event->getclaim()->inventoryItem->facebooksales->count() > 0 && $availableQty == 0) {
            \Log::info('Posting sold comment to multiple fb items!');
            try {
                foreach ($event->getclaim()->inventoryItem->facebooksales as $facebookSaleItem) {
                    // Ignore the facebook photo we've already posted to.
                    if ($facebookSaleItem->facebook_post_id == $shoppable->facebook_post_id) {
                        continue;
                    }
                    // if remaining qty is 0 and we have facebook sales, post comment to the sales
                    $this->handleFacebookCommentToPhoto($facebookSaleItem->facebook_post_id,  ['message' => 'Sold'], $seller->getFacebookUserToken());
                }
            } catch (Exception $e) {
                // event()
            }
        }

        $sellerEmail = $seller->email;
        $claimerEmail = $claimedBy->email;

        with(new KitEmail('inventory.claims.emails.claimed_toclaimer', ['item' => $event->getclaim()->inventoryItem], function($mailer) use ($claimerEmail) {
            $mailer->to($claimerEmail)->subject('Item claimed.');
        }))->send();

        if($seller->checkIsNotifyable('inventory_claimed', 'web')){
            with(new KitEmail('inventory.claims.emails.claimed_toseller', ['item' => $event->getclaim()->inventoryItem], function($mailer) use ($sellerEmail){
                $mailer->to($sellerEmail)->subject('Item claimed.');
            }))->send();
        }
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