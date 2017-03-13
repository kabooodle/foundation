<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Listings;

use DB;
use Carbon\Carbon;
use Kabooodle\Models\User;
use Kabooodle\Models\Listings;
use Kabooodle\Models\ListingItems;
use Kabooodle\Bus\Events\Listings\ListingScheduledEvent;
use Kabooodle\Bus\Commands\Listings\CreateOrUpdateCustomListingCommand;

/**
 * Class CreateOrUpdateCustomListingCommandHandler
 */
class CreateOrUpdateCustomListingCommandHandler extends AbstractScheduleListingsCommandHandler
{
    /**
     * @param CreateOrUpdateCustomListingCommand $command
     *
     * @return mixed
     */
    public function handle(CreateOrUpdateCustomListingCommand $command)
    {
        /** @var User $actor */
        $actor = $command->getActor();

        return DB::transaction(function () use ($actor, $command) {

            /** @var Listings $listing */
            $listing = $this->buildListing($command->getActor(), null, null, $command);

            $listingItems = $this->buildListingItems($listing, $command);

            $listing->listingItems()->saveMany($listingItems);

            $listing->save();

            event(new ListingScheduledEvent($actor->id, $listing->id));

            return $listing;
        });
    }

    /**
     * @param User $user
     * @param int|null $existingId
     * @param Carbon|null $scheduledFor
     * @param null $options
     * @return \Illuminate\Database\Eloquent\Model|Listings|null|static
     */
    public function buildListing(User $user, int $existingId = null,  Carbon $scheduledFor = null, $options = null)
    {
        // Insert or Update
        $listing = Listings::where('name', '=', $options->getSaleName())
            ->where('owner_id', '=', $user->id)->first();

        if (!$listing) {
            $listing = parent::buildListing($user, null, $this->now);
            $listing->name = $options->getSaleName();
            $listing->status = Listings::STATUS_COMPLETED;
            $listing->save();
        }

        return $listing;
    }

    /**
     * @param Listings                           $listing
     * @param CreateOrUpdateCustomListingCommand $command
     *
     * @return array
     */
    public function buildListingItems(Listings $listing, CreateOrUpdateCustomListingCommand $command)
    {
        $selectedItems = $command->getSelectedInventoryItems();
        $actor = $command->getActor();
        $listedItems = [];

        if (count($selectedItems) > 0) {
            foreach ($selectedItems as $selectedItem) {

                // Skip inventory items that do not belong to the user.
                // Skip items already in the flash sale by the user.
                if (!$this->listableItemBelongsToUser($selectedItem, $actor) || $this->itemAlreadyInSale($listing, $selectedItem)
                ) {
                    continue;
                }

                $listingItem = new ListingItems;
                $listingItem->listing_id = $listing->id;
                $listingItem->owner_id = $actor->id;
                $listingItem->listable_id = $selectedItem;

                // Copy the type and status from the parent listing.
                // Status may actually change and be different, below otherwise they start the same.
                $listingItem->type = $listing->type;
                $listingItem->status = $listing->status;
                $listingItem->status_updated_at = $this->now;
                $listingItem->make_available_at = $this->now;

                $listedItems[] = $listingItem;
            }
        }

        return $listedItems;
    }
}
