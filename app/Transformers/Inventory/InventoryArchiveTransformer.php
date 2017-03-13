<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Transformers\Inventory;

use Kabooodle\Models\Inventory;
use League\Fractal\TransformerAbstract;

/**
 * Class InventoryArchiveTransformer
 */
class InventoryArchiveTransformer extends TransformerAbstract
{
    /**
     * @param Inventory $inventory
     *
     * @return array
     */
    public function transform(Inventory $inventory)
    {
        return [
            'id' => $inventory->id,
            'name_with_cover_photo' => $inventory->name_with_variant.'::'.$inventory->coverimage->location,
            'name' => $inventory->name,
            'name_with_variant' => $inventory->name_with_variant,
            'archived_at' => $inventory->humanize($inventory->archived_at),
            'price_usd' => $inventory->price_usd,
            'type_id' => $inventory->type->id,
            'style_id' => $inventory->style->id,
            'style' => $inventory->style,
            'size' => $inventory->styleSize,
            'cover_image' => $inventory->coverimage,
            'qty_on_hold' => $inventory->getOnHoldQuantity(),
            'qty_on_hand' => $inventory->getAvailableQuantity(),
            'unarchive_endpoint' => apiRoute('listables.activate', $inventory->id),
        ];
    }
}
