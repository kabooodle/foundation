<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Transformers\Inventory;

use Kabooodle\Models\Inventory;
use League\Fractal\TransformerAbstract;

/**
 * Class InventoryTransformer
 * @package Kabooodle\Transformers\Inventory
 */
class InventoryTransformer extends TransformerAbstract
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
            'name' => $inventory->name,
            'name_with_variant' => $inventory->name_with_variant,
            'price_usd' => $inventory->price_usd,
            'type_id' => $inventory->type->id,
            'style_id' => $inventory->style->id,
            'style' => $inventory->style,
            'size' => $inventory->styleSize,
            'item' => $inventory->files,
            'pending' => $inventory->pendingClaims->count(),
            'sales' => $inventory->sales->count(),
            'qty_on_hold' => $inventory->getOnHoldQuantity(),
            'views' => $inventory->views->count(),
            'qty_on_hand' => $inventory->getAvailableQuantity(),
            'gross' => $inventory->acceptedClaims->sum('accepted_price')
        ];
    }
}
