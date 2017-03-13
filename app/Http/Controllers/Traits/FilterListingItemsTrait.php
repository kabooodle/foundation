<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Collection;
use Kabooodle\Models\InventoryGrouping;

/**
 * Class FilterListingItemsTrait
 * @package Kabooodle\Http\Controllers\Traits
 */
trait FilterListingItemsTrait
{
    /**
     * @param Collection $items
     * @param array $style_query
     * @param array $size_query
     * @param array $sellers_query
     *
     * @return Collection|static
     */
    protected function filterListingItems(Collection $items, array $style_query = [], array $size_query = [], array $sellers_query = [])
    {
        if (!empty($style_query)) {
            $items = $items->filter(function ($item) use ($style_query) {
                if (in_array($item->listedItem->inventory_type_styles_id, $style_query)
                    || (in_array('outfits', $style_query) && $item->listedItem instanceof InventoryGrouping)) {
                    return $item;
                }
                return false;
            });
        }

        if (!empty($size_query)) {
            $items = $items->filter(function ($item) use ($size_query, $style_query) {
                return in_array($item->listedItem->inventory_sizes_id, $size_query) || (in_array('outfits', $style_query) && $item->listedItem instanceof InventoryGrouping);
            });
        }

        if (!empty($sellers_query)) {
            $items = $items->filter(function ($item) use ($sellers_query, $style_query) {
                return in_array($item->owner_id, $sellers_query) || (in_array('outfits', $style_query) && $item->listedItem instanceof InventoryGrouping);
            });
        }

        $items = $items->sortBy(function ($item) {
            return $item->make_available_at;
        })->sortBy(function ($item) {
            return $item->id;
        })->values();

        return $items;
    }
}
