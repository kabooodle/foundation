<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Watchable;

use Kabooodle\Models\User;
use Kabooodle\Models\ListingItems;
use Kabooodle\Libraries\Emails\PiperEmail;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Watchable\NotifyWatcherInventoryQuantityUpdatedCommand;

/**
 * Class NotifyWatcherInventoryQuantityUpdatedCommandHandler
 */
class NotifyWatcherInventoryQuantityUpdatedCommandHandler
{
    use DispatchesJobs;

    /**
     * @param NotifyWatcherInventoryQuantityUpdatedCommand $command
     *
     * @return bool
     */
    public function handle(NotifyWatcherInventoryQuantityUpdatedCommand $command)
    {
        $watcher = $command->getWatcher();
        $listingItem = $command->getListingItem();

        if ($watcher->checkIsNotifyable('inventory_updated', 'email')) {
            $this->toMail($watcher, $listingItem);
        }
//        $this->toSMS($listingItem);

        return true;
    }

    /**
     * @param User         $user
     * @param ListingItems $listingItem
     */
    public function toMail(User $user, ListingItems $listingItem)
    {
        $email = new PiperEmail;
        $email->setView('listings.items.emails.quantityavailable')
            ->setParameters([
                'user' => $user,
                'listing' => $listingItem,
                'listing_link' => $this->getListingRoute($listingItem),
            ])
            ->setCallable(function ($m) use ($user) {
                $m->to($user->primaryEmail->address)
                    ->subject('Listing quantity now available');
            })
            ->send();
    }

    public function toWeb()
    {

    }

    public function toSMS(ListingItems $listingItem)
    {
//        $nexmo = app('Nexmo\Client');
//        $nexmo->message()->send([
//            'to' => '19163904522',
//            'from' => '96167',
//            'text' => 'Listing quantity now available.'.$this->getListingRoute($listingItem)
//        ]);
    }

    /**
     * @param ListingItems $listingItem
     *
     * @return string
     */
    public function getListingRoute(ListingItems $listingItem)
    {
        $route = route('externalclaim.show', [$listingItem->obfuscateIdToString()]);

        return str_replace(['api.', 'app.'], '', $route);
    }
}