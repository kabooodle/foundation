<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Presenters\Models\InventoryGrouping;

use Kabooodle\Models\ListingItems;
use Kabooodle\Presenters\PresenterAbstract;

/**
 * Class InventoryGroupingPresenter
 * @package Kabooodle\Presenters\Models\InventoryGrouping
 */
class InventoryGroupingPresenter extends PresenterAbstract
{
    public function listableShowOutfitSection(ListingItems $listingItem = null)
    {
        $inventory = $this->entity->inventoryItems;
        if ($listingItem) {
            $inventory = $inventory->filter(function ($item) use ($listingItem) {
                return $listingItem->listing->listables->contains($item);
            });
        }

        $data = [
            'listable' => $this->entity,
            'inventory' => $inventory,
            'listing' => $listingItem ? $listingItem->listing : null,
        ];

        return $inventory->count() ? view('inventory-groupings.partials._listables_outfit_div', $data) : null;
    }
}
