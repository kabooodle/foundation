<?php

namespace Kabooodle\Bus\Handlers\Events\Claim;

use Illuminate\Contracts\Mail\MailQueue;
use Kabooodle\Bus\Events\Claim\NewItemWasClaimedEvent;

/**
 * Class ItemWasClaimedEventHandler
 * @package Kabooodle\Bus\Handlers\Events\Claim
 */
class ItemWasClaimedEventHandler
{
    /**
     * ItemWasClaimedEventHandler constructor.
     *
     * @param MailQueue $mailer
     */
    public function __construct(MailQueue $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param NewItemWasClaimedEvent $event
     */
    public function handle(NewItemWasClaimedEvent $event)
    {
        // We need to email two people, the seller and the person who claimed the item.
        $claimedBy = $event->getclaim()->claimedBy->email;
        $seller = $event->getclaim()->inventoryItem->owner->email;

        $this->mailer->queue('inventory.claims.emails.claimed_toclaimer', ['item' => $event->getclaim()->inventoryItem], function($mailer) use ($claimedBy) {
            $mailer->to($claimedBy)->subject('Item claimed.');
        });

        $this->mailer->queue('inventory.claims.emails.claimed_toseller', ['item' => $event->getclaim()->inventoryItem], function($mailer) use ($seller){
            $mailer->to($seller)->subject('Item claimed.');
        });
    }
}