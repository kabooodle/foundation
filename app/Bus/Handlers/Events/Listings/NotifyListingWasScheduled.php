<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
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
final class NotifyListingWasScheduled implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param ListingScheduledEvent $event
     */
    public function handle(ListingScheduledEvent $event)
    {
        $recipient = $event->getActor();
        $listing = $event->getListing();

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
        $email = new KitEmail;
        $email->setView('listings.emails.newlisting')
            ->setCallable(function($m) use ($actor, $listing) {
                $m->to($actor->primaryEmail->address)
                    ->subject('You scheduled a new listing');
            })
            ->setParameters([
                'listing' => $listing,
                'actor' => $actor
            ])
            ->send();
    }

    /**
     * @param User     $actor
     * @param Listings $listing
     */
    public function toDatabase(User $actor, Listings $listing)
    {

    }
}
