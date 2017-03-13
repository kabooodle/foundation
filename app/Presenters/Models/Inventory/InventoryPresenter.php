<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Presenters\Models\Inventory;

use Kabooodle\Models\ListingItems;
use Kabooodle\Presenters\PresenterAbstract;

/**
 * Class InventoryPresenter
 * @package Kabooodle\Presenters\Models\Inventory
 */
class InventoryPresenter extends PresenterAbstract
{
    public function listableShowOutfitSection(ListingItems $listingItem = null)
    {
        $groupings = $this->entity->groupings;
        if ($listingItem) {
            $groupings = $groupings->filter(function ($grouping) use ($listingItem) {
                return $listingItem->listing->listables->contains($grouping);
            });
        }
        $data = [
            'listable' => $this->entity,
            'groupings' => $groupings,
            'listing' => $listingItem ? $listingItem->listing : null,
        ];

        return $groupings->count() ? view('inventory.partials._listables_outfits_div', $data) : null;
    }
}
