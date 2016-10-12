<?php

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
            'type_id' => $inventory->type->id,
            'style_id' => $inventory->style->id
        ];
    }
}