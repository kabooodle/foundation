<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Listings;

use Bugsnag;
use Exception;
use Kabooodle\Models\User;
use Illuminate\Bus\Queueable;
use Kabooodle\Models\Listings;
use Illuminate\Queue\SerializesModels;
use Kabooodle\Libraries\Emails\KitEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kabooodle\Bus\Events\Listings\ListingScheduledEvent;

/**
 * Class NotifyListingWasScheduled
 */
final class NotifyListingWasScheduled
{
    /**
     * @param ListingScheduledEvent $event
     */
    public function handle(ListingScheduledEvent $event)
    {
        $recipientId = $event->getActorId();
        $listingId = $event->getListingId();

        $listing = Listings::noEagerLoads()->findOrFail($listingId);
        $recipient = User::noEagerLoads()->findOrFail($recipientId);

        try {
            if ($recipient->primaryEmail && $recipient->primaryEmail->isVerified()) {
                $this->toEmail($recipient, $listing);
            }
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }
    }

    /**
     * @param User     $actor
     * @param Listings $listing
     */
    public function toEmail(User $actor, Listings $listing)
    {
        $emailAddress = $actor->primaryEmail->address;
        $listingItemsCount = $listing->items->count();
        $email = new KitEmail;
        $email->setView('listings.emails.newlisting')
            ->setCallable(function($m) use ($emailAddress, $listingItemsCount) {
                $m->to($emailAddress)
                    ->subject('You scheduled a new listing with '.$listingItemsCount.' items');
            })
            ->setParameters([
                'listing' => $listing,
                'timezone' => $actor->timezone
            ])
            ->send();
    }
}
