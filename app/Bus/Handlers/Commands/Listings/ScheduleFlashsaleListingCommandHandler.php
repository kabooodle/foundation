<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Listings;

use DB;
use Carbon\Carbon;
use Kabooodle\Models\ListingItems;
use Kabooodle\Models\User;
use Kabooodle\Models\Listings;
use Kabooodle\Models\FlashSales;
use Kabooodle\Bus\Events\Listings\ListingScheduledEvent;
use Kabooodle\Bus\Commands\Listings\ScheduleFlashsaleListingCommand;
use Kabooodle\Foundation\Exceptions\Listings\ListingUserIsNotSellerInFlashsaleException;

/**
 * Class ScheduleListingCommandHandler
 */
class ScheduleFlashsaleListingCommandHandler extends AbstractScheduleListingsCommandHandler
{
    /**
     * @var null
     */
    public $timeslot = null;

    /**
     * @var FlashSales
     */
    public $flashsale;

    /**
     * @param ScheduleFlashsaleListingCommand $command
     *
     * @return mixed
     */
    public function handle(ScheduleFlashsaleListingCommand $command)
    {
        // Set a timestamp of now we can reuse for consistency.
        $this->now = Carbon::now();

        /** @var User $actor */
        $actor = $command->getActor();

        /** @var Carbon $scheduledFor */
        $scheduledFor = $this->normalizeScheduledDateTime();

        $events = collect([]);

        DB::transaction(function () use ($actor, $scheduledFor, $command, $events) {
            /**
             * flashsale['listables'] array containing listables
             * flashsale['sale'] array containing Flashsale
             * flashsale['sale_id'] int of Flashsale id
             */
            foreach ($command->getFlashSales() as $flashSale) {

                /** @var FlashSales flashsale */
                $this->flashsale = FlashSales::with('sellerGroups', 'sellerGroups.users', 'admins', 'owner')
                    ->findOrFail($flashSale['sale_id']);

                /** @var Listings $listing */
                $listing = $this->buildListing($actor, $flashSale['sale_id'], $scheduledFor);

                $this->timeslot = $this->getSellerTimeslot($actor);

                // Use the same timeslot value for both.
                $this->timeslot = $this->timeslot;
                $listing->scheduled_for = $this->timeslot;

                $flashsaleInventoryItems = $this->buildListingItems($actor, $listing, $flashSale['listables']);

                $listing->listingItems()->saveMany($flashsaleInventoryItems);

                $listing->save();

                $events->push(new ListingScheduledEvent($actor->id, $listing->id));
            }
        });

        if ($events->count() > 0) {
            foreach($events as $event){
                event($event);
            }
        }
    }

    /**
     * @param User        $user
     * @param int|null    $existingId
     * @param Carbon|null $scheduledFor
     * @param null        $options
     *
     * @return \Illuminate\Database\Eloquent\Model|Listings|null|static
     */
    public function buildListing(User $user, int $existingId = null, Carbon $scheduledFor = null, $options = null)
    {
        // If this is a flash sale listing, we want to merge items into the flashsale instead of
        // creating new listings for the same flash sale/user.  So check first if the listing for the flashsale exists.
        $listing = Listings::where('flashsale_id', '=', $existingId)
            ->where('owner_id', '=', $user->id)->first();

        if (!$listing) {
            $listing = parent::buildListing($user, $existingId, $scheduledFor);
        }

        $listing->flashsale_id = $existingId;
        $listing->type = Listings::TYPE_FLASHSALE;

        $listing->save();

        return $listing;
    }

    /**
     * Flashsale listings are tricky.  A flashsale has "seller groups" - groups of users who are permitted to
     * sell/list items in a sale.  Each group has an optional "time slot"at which a groups items will "appear" within
     * a flash sale.  We need to make sure we identify the user's group and grab this (optional) date so that
     * we can assign it to the listing items->make_available_at
     *
     * @param User      $actor
     * @param Listings  $listing
     * @param array     $listables
     *
     * @throws ListingUserIsNotSellerInFlashsaleException
     * @return array
     */
    public function buildListingItems(User $actor, Listings $listing, array $listables)
    {
        $listedItems = [];

        // Loop over each of the items
        foreach ($listables as $listableItem) {
            // Skip inventory items that do not belong to the user.
            // Skip items already in the flash sale by the user.
            if (!$this->listableItemBelongsToUser($listableItem['id'], $actor)
                || $this->itemAlreadyInFlashsale($listing, $listableItem['id'])
            ) {
                continue;
            }

            $listingItem = new ListingItems;
            $listingItem->listing_id = $listing->id;
            $listingItem->owner_id = $actor->id;
            $listingItem->listable_id = $listableItem['id'];
            $listingItem->flashsale_id = $listing->flashsale_id;

            // Copy the type and status from the parent listing.
            // Status may actually change and be different, below otherwise they start the same.
            $listingItem->type = $listing->type;
            $listingItem->status = $listing->status;
            $listingItem->status_updated_at = $this->now;
            $listingItem->make_available_at = $this->timeslot;

            $listedItems[] = $listingItem;
        }

        return $listedItems;
    }

    /**
     * @param Listings $listing
     * @param int      $listableId
     *
     * @return mixed
     */
    protected function itemAlreadyInFlashsale(Listings $listing, int $listableId)
    {
        $listingItems = $listing->listingItems;

        return $listingItems->filter(function ($listingItem) use ($listableId) {
            return $listingItem->listable_id == $listableId;
        })->first();
    }

    /**
     * @param User $user
     *
     * @return Carbon
     * @throws ListingUserIsNotSellerInFlashsaleException
     */
    public function getSellerTimeslot(User $user)
    {
        // If the user is an admin of the flashsale, then the timeslot is now regardless of
        // whether they are posting early or late.
        if ($this->isActorAdmin($this->flashsale, $user)) {
            $timeslot = $this->now;
        } else {
            // IF they are not an admin, does the user belong to a seller group
            if (!$group = $this->getFlashsaleSellerGroupForUser($this->flashsale, $user)) {
                // If they aren't an admin, or in a group, they can't post.
                // This could trigger when they are removed from the group while trying to list.
                throw new ListingUserIsNotSellerInFlashsaleException;
            } else {
                // Timeslot assigned to them (nullable).
                $timeslot = $group->pivot->time_slot;

                // If the assigned timeslot is null, then they too can have timestamps of now, just like admins,
                // regardless of whether or not they are posting before the sale start.
                if (!$timeslot || is_null($timeslot)) {
                    $timeslot = $this->now;
                } else {
                    // If they were assigned a time slot but they are posting after their time slot
                    // We wont honor their time slot and instead use a timestamp of now.
                    if ($timeslot && $this->isUserListingLaterThanTimeSlot($timeslot)) {
                        $timeslot = $this->now;
                    }
                }
            }
        }

        return $timeslot;
    }

    /**
     * @param FlashSales $flashsale
     * @param User       $actor
     *
     * @return mixed
     */
    public function getFlashsaleSellerGroupForUser(FlashSales $flashsale, User $actor)
    {
        return $flashsale->getFlashsaleSellerGroupForUser($actor->id);
    }

    /**
     * @param FlashSales $flashsale
     * @param User       $actor
     *
     * @return bool
     */
    public function isActorAdmin(FlashSales $flashsale, User $actor)
    {
        return $flashsale->canSellerListToFlashsaleAnytime($actor->id);
    }

    /**
     * @param $timeslot
     *
     * @return bool
     */
    public function isUserListingLaterThanTimeSlot($timeslot)
    {
        return Carbon::now() > Carbon::parse($timeslot);
    }
}
