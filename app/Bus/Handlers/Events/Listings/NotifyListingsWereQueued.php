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
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kabooodle\Libraries\WebSockets\WebSocket;
use Kabooodle\Bus\Events\Listings\ListingsWereQueued;

/**
 * Class NotifyListingsWereQueued
 */
class NotifyListingsWereQueued implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param ListingsWereQueued $event
     */
    public function handle(ListingsWereQueued $event)
    {
        // Collection of Listings
        $listings = Listings::whereIn('id', $event->getListings())->get();

        foreach ($listings as $listing) {

            try {
                /** @var Listings $listing */
                $listing = $listing->fresh();

                /** @var User $owner */
                $owner = $listing->owner;

                if ($owner->primaryEmail && $owner->primaryEmail->isVerified()) {
                    if ($owner->checkIsNotifyable('listing_processing', 'email')) {
                        $this->toEmail($owner, $listing);
                    }
                }

                $this->toWeb($owner, $listing);
            } catch (Exception $e) {
                Bugsnag::notifyException($e);
            }
        }
    }


    public function toDatabase()
    {

    }

    /**
     * @param User     $user
     * @param Listings $listing
     */
    public function toEmail(User $user, Listings $listing)
    {
//        $email = new KitEmail;
//        $email->setView('emails.verification.claim')
//                ->setParameters([
//
//                ])
//                ->setCallable(function ($m) use ($user) {
//                    $m->to($user->primaryEmail->address)
//                        ->subject('Verify your '.env('APP_NAME').' claim');
//                })
//                ->send();
    }

    /**
     * Send an updated payload of the listing to the web. This will auto-update the corresponding listings
     * on the listings page' HTML. (Namely, we want the user to see the status update!)
     *
     * @param User     $user
     * @param Listings $listing
     */
    public function toWeb(User $user, Listings $listing)
    {
        $html = view('listings.partials._indexrow')->with('listing', $listing)->render();

        $pusher = new WebSocket;
        $pusher->setChannelName('private.'.env('APP_ENV').'.listings.'.$user->id)
            ->setEventName('listing:updated')
            ->setPayload([
                'id' => $listing->id,
                'html' => $html
            ])
            ->send();
    }
}
