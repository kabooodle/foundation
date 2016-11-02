<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
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
            'price_usd' => $inventory->price_usd,
            'initial_qty' => $inventory->getAvailableQuantity(),
            'type_id' => $inventory->type->id,
            'style_id' => $inventory->style->id,
            'style' => $inventory->style,
            'size' => $inventory->styleSize,
            'item' => $inventory->files,
            'pending' => $inventory->pendingClaims,
            'sales' => $inventory->sales
        ];
    }
}