<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Listings;

use DB;
use Carbon\Carbon;
use Kabooodle\Models\User;
use Kabooodle\Models\Listings;
use Kabooodle\Models\FlashSales;
use Kabooodle\Models\ListingItems;
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

        /** @var FlashSales flashsale */
        $this->flashsale = FlashSales::with('sellerGroups', 'sellerGroups.users', 'admins', 'owner')
            ->findOrFail($command->getFlashSaleId());

        return DB::transaction(function () use ($actor, $scheduledFor, $command) {

            /** @var Listings $listing */
            $listing = $this->buildListing($command, $scheduledFor);

            $this->timeslot = $this->getSellerTimeslot($actor);
            // Use the same timeslot value for both.
            $this->timeslot = $this->timeslot;
            $listing->scheduled_for = $this->timeslot;

            $flashsaleInventoryItems = $this->buildListingItems($listing, $command);

            $listing->listingItems()->saveMany($flashsaleInventoryItems);

            $listing->save();

            event(new ListingScheduledEvent($actor->id, $listing->id));

            return $listing;
        });
    }

    /**
     * @param ScheduleListingCommand $command
     * @param Carbon                 $scheduledFor
     *
     * @return Listings
     */
    public function buildListing($command, Carbon $scheduledFor = null)
    {
        // If this is a flash sale listing, we want to merge items into the flashsale instead of
        // creating new listings for the same flash sale/user.  So check first if the listing for the flashsale exists.
        $listing = Listings::where('flashsale_id', '=', $command->getFlashSaleId())
            ->where('owner_id', '=', $command->getActor()->id)->first();

        if (!$listing) {
            $listing = parent::buildListing($command, $scheduledFor);
        }

        $listing->flashsale_id = $command->getFlashSaleId();
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
     *
     * @param Listings               $listing
     * @param ScheduleFlashsaleListingCommand $command
     *
     * @throws ListingUserIsNotSellerInFlashsaleException
     * @return array
     */
    public function buildListingItems(Listings $listing, ScheduleFlashsaleListingCommand $command)
    {
        $selectedItems = $command->getSelectedItems();
        $actor = $command->getActor();
        $listedItems = [];

        if (count($selectedItems) > 0) {
            foreach ($selectedItems as $selectedItem) {

                // Skip inventory items that do not belong to the user.
                // Skip items already in the flash sale by the user.
                if (!$this->inventoryItemBelongsToUser($selectedItem['id'], $actor) || $this->itemAlreadyInFlashsale($listing, $selectedItem['id'])
                ) {
                    continue;
                }

                $listingItem = new ListingItems;
                $listingItem->listing_id = $listing->id;
                $listingItem->owner_id = $actor->id;
                $listingItem->inventory_id = $selectedItem['id'];
                $listingItem->flashsale_id = $command->getFlashSaleId();

                // Copy the type and status from the parent listing.
                // Status may actually change and be different, below otherwise they start the same.
                $listingItem->type = $listing->type;
                $listingItem->status = $listing->status;
                $listingItem->status_updated_at = $this->now;
                $listingItem->make_available_at = $this->timeslot;

                $listedItems[] = $listingItem;
            }
        }

        return $listedItems;
    }

    /**
     * @param Listings $listing
     * @param int      $inventoryId
     *
     * @return mixed
     */
    protected function itemAlreadyInFlashsale(Listings $listing, int $inventoryId)
    {
        $listingItems = $listing->listingItems;

        return $listingItems->filter(function ($listingItem) use ($inventoryId) {
            return $listingItem->inventory_id == $inventoryId;
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
                if (! $timeslot || is_null($timeslot)) {
                    $timeslot =  $this->now;
                } else {
                    // If they were assigned a time slot but they are posting after their time slot
                    // We wont honor their time slot and instead use a timestamp of now.
                    if ($timeslot && $this->isUserListingLaterThanTimeSlot($timeslot)) {
                        $timeslot =  $this->now;
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
